<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\BookingEnquiryMail;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Setting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $rooms = Room::active()->with('roomType')->get();
        $selectedRoom = null;

        if ($request->has('room') && $request->room) {
            $selectedRoom = Room::where('id', $request->room)->first();
        }

        $settings = [
            'currency_symbol' => Setting::get('currency_symbol', '$'),
            'check_in_time' => Setting::get('check_in_time', '15:00'),
            'check_out_time' => Setting::get('check_out_time', '11:00'),
        ];

        return view('frontend.booking.create', compact('rooms', 'selectedRoom', 'settings'));
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
        ]);

        $rooms = Room::active()
            ->where('max_adults', '>=', $request->adults)
            ->get();

        $availableRooms = $rooms->filter(function ($room) use ($request) {
            return $room->isAvailable($request->check_in, $request->check_out);
        })->values();

        return response()->json([
            'success' => true,
            'rooms' => $availableRooms->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'price' => $r->price_per_night,
                'image' => $r->featured_image,
            ])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'integer|min:0',
            'guest_first_name' => 'required|string',
            'guest_last_name' => 'required|string',
            'guest_email' => 'required|email',
            'guest_phone' => 'nullable|string',
            'special_requests' => 'nullable|string',
        ]);

        $room = Room::findOrFail($request->room_id);

        if (!$room->isAvailable($request->check_in, $request->check_out)) {
            return back()->withErrors(['room' => 'Room not available for selected dates']);
        }

        $nights = \Carbon\Carbon::parse($request->check_out)->diffInDays(\Carbon\Carbon::parse($request->check_in));
        $pricePerNight = $room->price_per_night;
        $subtotal = $nights * $pricePerNight;
        $taxRate = (float) Setting::get('tax_rate', 10) / 100;
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;

        $guest = Guest::firstOrCreate(
            ['email' => $request->guest_email],
            [
                'first_name' => $request->guest_first_name,
                'last_name' => $request->guest_last_name,
                'phone' => $request->guest_phone,
            ]
        );

        $booking = Booking::create([
            'room_id' => $request->room_id,
            'guest_id' => $guest->id,
            'guest_first_name' => $request->guest_first_name,
            'guest_last_name' => $request->guest_last_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'nights' => $nights,
            'adults' => $request->adults,
            'children' => $request->children ?? 0,
            'price_per_night' => $pricePerNight,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'special_requests' => $request->special_requests,
            'source' => 'website',
        ]);

        $confirmationUrl = URL::temporarySignedRoute(
            'booking.confirmation',
            now()->addHour(),
            ['id' => Crypt::encryptString((string) $booking->id)]
        );

        $staffEmail = Setting::get('booking_enquiry_email') ?: Setting::get('hotel_email');

        if ($staffEmail) {
            try {
                Mail::to($staffEmail)->send(new BookingEnquiryMail($booking, $confirmationUrl));
            } catch (\Throwable $e) {
                Log::error('Failed to send booking enquiry email to staff', [
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        return redirect($confirmationUrl);
    }

    public function confirmation(string $id)
    {
        try {
            $bookingId = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $booking = Booking::with('room')->findOrFail($bookingId);
        return view('frontend.booking.confirmation', compact('booking'));
    }

    public function cancel(Booking $booking, string $token)
    {
        if ($booking->cancellation_token !== $token) {
            abort(403);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return view('frontend.booking.cancelled', compact('booking'));
    }
}
