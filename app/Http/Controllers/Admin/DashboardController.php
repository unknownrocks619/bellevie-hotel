<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_rooms' => Room::count(),
            'active_rooms' => Room::active()->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'total_guests' => Guest::count(),
            'new_guests_this_month' => Guest::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'revenue_this_month' => Booking::where('status', 'checked_out')->whereMonth('created_at', now()->month)->sum('total_amount'),
            'revenue_last_month' => Booking::where('status', 'checked_out')->whereMonth('created_at', now()->subMonth()->month)->sum('total_amount'),
            'occupancy_rate' => $this->calculateOccupancyRate(),
        ];

        $recentBookings = Booking::with(['room', 'guest'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $checkInsToday = Booking::today()->where('status', 'pending')->with('room', 'guest')->get();
        $checkOutsToday = Booking::checkingOut()->where('status', 'checked_in')->with('room', 'guest')->get();

        $monthlyRevenue = Booking::where('status', 'checked_out')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->get();

        $vipGuests = Guest::orderByDesc('total_spent')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'checkInsToday', 'checkOutsToday', 'monthlyRevenue', 'vipGuests'));
    }

    private function calculateOccupancyRate(): float
    {
        $totalRooms = Room::active()->count();
        if ($totalRooms === 0) return 0;

        $occupiedRooms = Booking::where('status', 'checked_in')
            ->count();

        return ($occupiedRooms / $totalRooms) * 100;
    }
}
