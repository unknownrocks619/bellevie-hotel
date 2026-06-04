<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['room', 'guest']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_reference', 'like', "%{$request->search}%")
                    ->orWhere('guest_email', 'like', "%{$request->search}%")
                    ->orWhere('guest_first_name', 'like', "%{$request->search}%")
                    ->orWhere('guest_last_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('check_in', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('check_out', '<=', $request->date_to);
        }

        $bookings = $query->orderByDesc('created_at')->paginate(20);

        $statusCounts = [
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'checked_out' => Booking::where('status', 'checked_out')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'statusCounts'));
    }

    public function calendar()
    {
        $events = Booking::whereIn('status', ['confirmed', 'checked_in', 'checked_out', 'pending'])
            ->with('room')
            ->get()
            ->map(function ($booking) {
                $colors = [
                    'pending'     => '#ffc107',
                    'confirmed'   => '#17a2b8',
                    'checked_in'  => '#28a745',
                    'checked_out' => '#6c757d',
                ];
                return [
                    'id'    => $booking->id,
                    'title' => $booking->guest_first_name . ' ' . $booking->guest_last_name . ' – ' . ($booking->room->name ?? ''),
                    'start' => $booking->check_in,
                    'end'   => $booking->check_out,
                    'color' => $colors[$booking->status] ?? '#C9A227',
                    'url'   => route('admin.bookings.show', $booking),
                ];
            });

        return view('admin.bookings.calendar', compact('events'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['room', 'guest']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        if ($request->notes) {
            $booking->update(['internal_notes' => $request->notes]);
        }

        // Update guest stats on checkout
        if ($request->status === 'checked_out' && $oldStatus !== 'checked_out') {
            if ($booking->guest) {
                $booking->guest->increment('total_stays');
                $booking->guest->increment('total_spent', $booking->total_amount);
                $booking->guest->update(['last_stay_at' => now()]);
            }
        }

        return back()->with('success', 'Booking status updated');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted');
    }

    public function export()
    {
        $bookings = Booking::with(['room', 'guest'])->get();

        $csv = "Booking Reference,Guest Name,Email,Phone,Room,Check-in,Check-out,Nights,Adults,Children,Total Amount,Status,Payment Status\n";

        foreach ($bookings as $booking) {
            $csv .= "\"{$booking->booking_reference}\",\"{$booking->guest_first_name} {$booking->guest_last_name}\",\"{$booking->guest_email}\",\"{$booking->guest_phone}\",\"{$booking->room->name}\",\"{$booking->check_in}\",\"{$booking->check_out}\",{$booking->nights},{$booking->adults},{$booking->children},{$booking->total_amount},\"{$booking->status}\",\"{$booking->payment_status}\"\n";
        }

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="bookings.csv"');
    }

    public function create(Request $request)
    {
        $rooms = \App\Models\Room::active()->with('roomType')->orderBy('name')->get();
        $date  = $request->get('date', today()->format('Y-m-d'));
        return view('admin.bookings.create', compact('rooms', 'date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'guest_first_name' => 'required|string',
            'guest_last_name'  => 'required|string',
            'guest_email'      => 'required|email',
            'guest_phone'      => 'required|string',
            'check_in'         => 'required|date',
            'check_out'        => 'required|date|after:check_in',
            'adults'           => 'required|integer|min:1',
            'children'         => 'nullable|integer|min:0',
            'status'           => 'required|in:pending,confirmed,checked_in',
            'payment_status'   => 'nullable|in:pending,paid,partial',
            'special_requests' => 'nullable|string',
            'internal_notes'   => 'nullable|string',
        ]);

        $room   = \App\Models\Room::findOrFail($request->room_id);
        $nights = (int) \Carbon\Carbon::parse($request->check_in)->diffInDays($request->check_out);
        $subtotal = $nights * $room->price_per_night;

        // Find or create guest
        $guest = \App\Models\Guest::firstOrCreate(
            ['email' => $request->guest_email],
            [
                'first_name' => $request->guest_first_name,
                'last_name'  => $request->guest_last_name,
                'phone'      => $request->guest_phone,
            ]
        );

        $booking = \App\Models\Booking::create([
            'room_id'          => $room->id,
            'guest_id'         => $guest->id,
            'guest_first_name' => $request->guest_first_name,
            'guest_last_name'  => $request->guest_last_name,
            'guest_email'      => $request->guest_email,
            'guest_phone'      => $request->guest_phone,
            'check_in'         => $request->check_in,
            'check_out'        => $request->check_out,
            'nights'           => $nights,
            'adults'           => $request->adults,
            'children'         => $request->children ?? 0,
            'price_per_night'  => $room->price_per_night,
            'subtotal'         => $subtotal,
            'tax_amount'       => 0,
            'total_amount'     => $subtotal,
            'status'           => $request->status,
            'payment_status'   => $request->payment_status ?? 'pending',
            'special_requests' => $request->special_requests,
            'internal_notes'   => $request->internal_notes,
            'source'           => 'admin',
        ]);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking created successfully.');
    }
}
