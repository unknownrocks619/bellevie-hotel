@extends('layouts.admin')
@section('page-title', 'Bookings')

@section('content')
<div class="row mb-3 align-items-center">
    <div class="col">
        <a href="{{ route('admin.bookings.calendar') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-calendar3"></i> Calendar View
        </a>
        <a href="{{ route('admin.bookings.export') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
    <div class="col-auto">
        <form method="GET" class="d-inline-flex gap-2 align-items-center">
            <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['pending','confirmed','checked_in','checked_out','cancelled','no_show'] as $s)
                    <option value="{{ $s }}" {{ request('status')==$s ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_',' ',$s)) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<!-- Status Stats Bar -->
@if(isset($statusCounts))
<div class="row mb-3">
    @foreach(['pending'=>'warning','confirmed'=>'success','checked_in'=>'info','checked_out'=>'secondary','cancelled'=>'danger'] as $status => $color)
    <div class="col-md-2 col-sm-4 col-6">
        <div class="p-2 rounded text-center border" style="cursor:pointer; background: #f8f9fa;" onclick="filterStatus('{{$status}}')">
            <div class="fw-bold">{{ $statusCounts[$status] ?? 0 }}</div>
            <small class="text-muted">{{ ucfirst(str_replace('_',' ',$status)) }}</small>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Nights</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="fw-bold text-decoration-none" style="color:#C9A227;">
                            {{ $booking->booking_reference }}
                        </a>
                    </td>
                    <td>
                        {{ $booking->guest_first_name }} {{ $booking->guest_last_name }}<br>
                        <small class="text-muted">{{ $booking->guest_email }}</small>
                    </td>
                    <td>{{ $booking->room->name ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</td>
                    <td class="text-center">{{ $booking->nights }}</td>
                    <td>${{ number_format($booking->total_amount, 2) }}</td>
                    <td>
                        @php
                            $colors = [
                                'pending'     => 'warning text-dark',
                                'confirmed'   => 'success',
                                'checked_in'  => 'info text-dark',
                                'checked_out' => 'secondary',
                                'cancelled'   => 'danger',
                                'no_show'     => 'dark',
                            ];
                        @endphp
                        <span class="badge bg-{{ $colors[$booking->status] ?? 'secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </td>
                    <td><small>{{ ucfirst($booking->source ?? 'website') }}</small></td>
                    <td>
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-xs btn-outline-primary btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">No bookings found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $bookings->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function filterStatus(status) {
    const url = new URL(window.location);
    url.searchParams.set('status', status);
    window.location = url.toString();
}
</script>
@endsection
