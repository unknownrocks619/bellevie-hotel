@extends('layouts.admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <h3>{{ $stats['total_rooms'] }}</h3>
            <p>Total Rooms</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <h3>{{ $stats['checked_in'] }}</h3>
            <p>Checked In</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <h3>${{ number_format($stats['revenue_this_month'], 0) }}</h3>
            <p>Monthly Revenue</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <h3>{{ round($stats['occupancy_rate']) }}%</h3>
            <p>Occupancy Rate</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <h3>{{ $stats['pending_bookings'] }}</h3>
            <p>Pending Bookings</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <h3>{{ $stats['confirmed_bookings'] }}</h3>
            <p>Confirmed</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <h3>{{ $stats['total_guests'] }}</h3>
            <p>Total Guests</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <h3>{{ $stats['new_guests_this_month'] }}</h3>
            <p>New This Month</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Check-ins Today</div>
            <div class="card-body">
                @if($checkInsToday->count())
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Nights</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checkInsToday as $booking)
                                <tr>
                                    <td>{{ $booking->guest_first_name }} {{ $booking->guest_last_name }}</td>
                                    <td>{{ $booking->room->name }}</td>
                                    <td>{{ $booking->nights }}</td>
                                    <td>${{ $booking->total_amount }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No check-ins today</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">Revenue Trend</div>
            <div class="card-body">
                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">VIP Guests</div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @foreach($vipGuests as $guest)
                <div class="mb-3 pb-3 border-bottom">
                    <h6 class="mb-1">{{ $guest->getFullName() }}</h6>
                    <p class="mb-1" style="font-size: 0.85rem; color: #666;">
                        <span class="badge bg-warning text-dark">{{ ucfirst($guest->vip_status) }}</span>
                    </p>
                    <p class="mb-0" style="font-size: 0.85rem; color: #666;">
                        Total Spent: <strong>${{ number_format($guest->total_spent, 2) }}</strong>
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueData = @json($monthlyRevenue->mapWithKeys(fn($m) => ["Month " . $m->month => $m->revenue]));
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: Object.keys(revenueData),
        datasets: [{
            label: 'Revenue',
            data: Object.values(revenueData),
            backgroundColor: '#C9A227',
            borderColor: '#C9A227',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection
