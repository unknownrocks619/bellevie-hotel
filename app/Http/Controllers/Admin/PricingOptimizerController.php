<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PricingOptimizerController extends Controller
{
    // How many days to wait after applying a price change before recommending again
    private const COOLDOWN_DAYS = 14;

    public function index()
    {
        $rooms = Room::with('roomType')->where('is_active', true)->get();

        $recommendations = $rooms->map(function ($room) {
            return $this->analyzeRoom($room);
        });

        $stats = [
            'total_rooms'      => $rooms->count(),
            'rooms_to_raise'   => $recommendations->where('action', 'increase')->count(),
            'rooms_to_lower'   => $recommendations->where('action', 'decrease')->count(),
            'rooms_optimal'    => $recommendations->where('action', 'maintain')->count(),
            'rooms_monitoring' => $recommendations->where('action', 'monitoring')->count(),
            'avg_occupancy'    => $recommendations->whereNotIn('action', ['monitoring'])->avg('occupancy_rate') ?? 0,
        ];

        return view('admin.pricing.index', compact('recommendations', 'stats'));
    }

    public function apply(Request $request, Room $room)
    {
        $request->validate([
            'new_price' => 'required|numeric|min:1',
        ]);

        $oldPrice = $room->price_per_night;
        $room->update(['price_per_night' => $request->new_price]);

        // Store cooldown with metadata so we can show "monitoring" state
        Cache::put("pricing_cooldown_{$room->id}", [
            'applied_at'  => now()->toDateTimeString(),
            'expires_at'  => now()->addDays(self::COOLDOWN_DAYS)->toDateTimeString(),
            'old_price'   => (float) $oldPrice,
            'new_price'   => (float) $request->new_price,
        ], now()->addDays(self::COOLDOWN_DAYS));

        return back()->with('success', "Price for \"{$room->name}\" updated from \${$oldPrice} to \${$request->new_price}. Monitoring performance for " . self::COOLDOWN_DAYS . " days before next recommendation.");
    }

    public function reset(Request $request, Room $room)
    {
        $cooldown = Cache::get("pricing_cooldown_{$room->id}");

        if (!$cooldown) {
            return back()->with('error', "No price change history found for \"{$room->name}\".");
        }

        $originalPrice = $cooldown['old_price'];
        $currentPrice  = $room->price_per_night;

        $room->update(['price_per_night' => $originalPrice]);
        Cache::forget("pricing_cooldown_{$room->id}");

        return back()->with('success', "Price for \"{$room->name}\" reset from \${$currentPrice} back to original \${$originalPrice}. Ready for fresh analysis.");
    }

    public function clearCooldown(Request $request, Room $room)
    {
        Cache::forget("pricing_cooldown_{$room->id}");

        return back()->with('success', "Monitoring period cleared for \"{$room->name}\". Fresh recommendations will now be generated based on current data.");
    }

    private function analyzeRoom(Room $room): array
    {
        // Check if this room is in a post-change monitoring cooldown
        $cooldown = Cache::get("pricing_cooldown_{$room->id}");
        if ($cooldown) {
            $expiresAt   = Carbon::parse($cooldown['expires_at']);
            $appliedAt   = Carbon::parse($cooldown['applied_at']);
            $daysLeft    = (int) now()->diffInDays($expiresAt, false);
            $daysLeft    = max(1, $daysLeft);
            $daysElapsed = (int) $appliedAt->diffInDays(now());

            return [
                'room'              => $room,
                'action'            => 'monitoring',
                'occupancy_rate'    => null,
                'booked_nights'     => null,
                'upcoming_bookings' => null,
                'urgent_bookings'   => null,
                'cancellation_rate' => null,
                'recent_revenue'    => null,
                'current_price'     => (float) $room->price_per_night,
                'recommended_price' => null,
                'change_percent'    => null,
                'reason'            => null,
                'cooldown'          => [
                    'applied_at'  => $cooldown['applied_at'],
                    'old_price'   => $cooldown['old_price'],
                    'new_price'   => $cooldown['new_price'],
                    'days_left'   => $daysLeft,
                    'days_elapsed'=> $daysElapsed,
                    'total_days'  => self::COOLDOWN_DAYS,
                ],
            ];
        }

        $today  = Carbon::today();
        $next30 = Carbon::today()->addDays(30);
        $next7  = Carbon::today()->addDays(7);
        $past30 = Carbon::today()->subDays(30);

        // Upcoming confirmed/checked-in bookings in next 30 days
        $upcomingBookings = Booking::where('room_id', $room->id)
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->where(function ($q) use ($today, $next30) {
                $q->whereBetween('check_in', [$today, $next30])
                  ->orWhereBetween('check_out', [$today, $next30])
                  ->orWhere(function ($q2) use ($today, $next30) {
                      $q2->where('check_in', '<=', $today)->where('check_out', '>=', $next30);
                  });
            })
            ->count();

        // Total nights booked in next 30 days
        $bookedNights = Booking::where('room_id', $room->id)
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->where('check_in', '<', $next30)
            ->where('check_out', '>', $today)
            ->get()
            ->sum(function ($booking) use ($today, $next30) {
                $start = max(Carbon::parse($booking->check_in), $today);
                $end   = min(Carbon::parse($booking->check_out), $next30);
                return max(0, $start->diffInDays($end));
            });

        $occupancyRate = round(($bookedNights / 30) * 100, 1);

        // Revenue in last 30 days
        $recentRevenue = Booking::where('room_id', $room->id)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->where('check_in', '>=', $past30)
            ->sum('total_amount');

        // Cancellation rate
        $totalBookings    = Booking::where('room_id', $room->id)->where('check_in', '>=', $past30)->count();
        $cancelledCount   = Booking::where('room_id', $room->id)->where('status', 'cancelled')->where('check_in', '>=', $past30)->count();
        $cancellationRate = $totalBookings > 0 ? round(($cancelledCount / $totalBookings) * 100, 1) : 0;

        // Bookings in next 7 days (urgency)
        $urgentBookings = Booking::where('room_id', $room->id)
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->where('check_in', '>=', $today)
            ->where('check_in', '<=', $next7)
            ->count();

        [$action, $changePercent, $reason] = $this->getRecommendation(
            $occupancyRate, $cancellationRate, $urgentBookings, $upcomingBookings
        );

        $currentPrice     = (float) $room->price_per_night;
        $recommendedPrice = round($currentPrice * (1 + $changePercent / 100), 2);

        return [
            'room'              => $room,
            'occupancy_rate'    => $occupancyRate,
            'booked_nights'     => $bookedNights,
            'upcoming_bookings' => $upcomingBookings,
            'urgent_bookings'   => $urgentBookings,
            'cancellation_rate' => $cancellationRate,
            'recent_revenue'    => $recentRevenue,
            'current_price'     => $currentPrice,
            'recommended_price' => $recommendedPrice,
            'change_percent'    => $changePercent,
            'action'            => $action,
            'reason'            => $reason,
            'cooldown'          => null,
        ];
    }

    private function getRecommendation(float $occupancy, float $cancellationRate, int $urgent, int $upcoming): array
    {
        if ($occupancy >= 95) {
            return ['increase', 20, 'Extremely high demand — room nearly fully booked. Strong opportunity to raise rate.'];
        }
        if ($occupancy >= 80) {
            return ['increase', 12, 'High occupancy rate. Demand is strong — consider raising rate to maximise revenue.'];
        }
        if ($occupancy >= 60) {
            return ['increase', 5, 'Good occupancy. Moderate demand — a small price increase may boost revenue.'];
        }

        // Urgent bookings in next 7 days despite low overall occupancy
        if ($urgent >= 2 && $occupancy < 40) {
            return ['increase', 8, 'Short-term demand surge detected. Several bookings due in next 7 days.'];
        }

        // Medium occupancy — maintain
        if ($occupancy >= 35) {
            return ['maintain', 0, 'Occupancy is healthy. Current rate is well-positioned for demand.'];
        }

        // Low demand — lower to attract bookings
        if ($occupancy < 15) {
            if ($cancellationRate > 30) {
                return ['decrease', -15, 'Very low bookings and high cancellation rate. A significant discount may help fill the room.'];
            }
            return ['decrease', -10, 'Low bookings — consider a 10% discount to attract more guests.'];
        }

        return ['decrease', -5, 'Below-average bookings. A small discount could improve occupancy.'];
    }
}
