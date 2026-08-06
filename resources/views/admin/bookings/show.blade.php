@extends('layouts.admin')
@section('page-title', 'Booking — ' . $booking->booking_reference)

@section('content')
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

<div class="row">
    {{-- Left column --}}
    <div class="col-lg-8">

        {{-- Booking details --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-check me-2"></i>Booking Details</span>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#emailConfirmationModal">
                        <i class="bi bi-envelope-check me-1"></i>Email Confirmation
                    </button>
                    <span class="badge bg-{{ $colors[$booking->status] ?? 'secondary' }} fs-6">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="mb-2"><span class="text-muted">Reference</span><br>
                            <strong class="fs-5" style="color:#C9A227;">{{ $booking->booking_reference }}</strong></p>
                        <p class="mb-2"><span class="text-muted">Guest Name</span><br>
                            <strong>{{ $booking->guest_first_name }} {{ $booking->guest_last_name }}</strong></p>
                        <p class="mb-2"><span class="text-muted">Email</span><br>{{ $booking->guest_email }}</p>
                        <p class="mb-2"><span class="text-muted">Phone</span><br>{{ $booking->guest_phone ?? '—' }}</p>
                        <p class="mb-0"><span class="text-muted">Source</span><br>
                            <span class="badge bg-light text-dark border">{{ ucfirst($booking->source ?? 'website') }}</span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><span class="text-muted">Room</span><br>
                            <strong>{{ $booking->room->name ?? '—' }}</strong>
                            @if($booking->room) <small class="text-muted">(#{{ $booking->room->room_number }})</small> @endif
                        </p>
                        <p class="mb-2"><span class="text-muted">Check-in</span><br>
                            <strong>{{ \Carbon\Carbon::parse($booking->check_in)->format('D, M d Y') }}</strong></p>
                        <p class="mb-2"><span class="text-muted">Check-out</span><br>
                            <strong>{{ \Carbon\Carbon::parse($booking->check_out)->format('D, M d Y') }}</strong></p>
                        <p class="mb-2"><span class="text-muted">Duration</span><br>{{ $booking->nights }} night(s)</p>
                        <p class="mb-0"><span class="text-muted">Guests</span><br>
                            {{ $booking->adults }} adult(s){{ $booking->children ? ', '.$booking->children.' child(ren)' : '' }}</p>
                    </div>
                </div>
                @if($booking->special_requests)
                <hr>
                <p class="mb-0"><span class="text-muted">Special Requests</span><br>{{ $booking->special_requests }}</p>
                @endif
            </div>
        </div>

        {{-- Payment Summary --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-receipt me-2"></i>Payment Summary</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td>{{ $booking->nights }} night(s) × ${{ number_format($booking->price_per_night, 2) }}</td>
                            <td class="text-end">${{ number_format($booking->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tax & Fees</td>
                            <td class="text-end text-muted">${{ number_format($booking->tax_amount, 2) }}</td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td>Total</td>
                            <td class="text-end" style="color:#C9A227;">${{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Bookings
        </a>
    </div>

    {{-- Right column --}}
    <div class="col-lg-4">

        {{-- Update Status --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-arrow-repeat me-2"></i>Update Status</div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select mb-3">
                        @foreach(['pending','confirmed','checked_in','checked_out','cancelled','no_show'] as $s)
                        <option value="{{ $s }}" {{ $booking->status == $s ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn w-100 text-white" style="background:#C9A227;">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-lightning me-2"></i>Quick Links</div>
            <div class="card-body d-grid gap-2">
                @if($booking->guest_id)
                <a href="{{ route('admin.guests.show', $booking->guest_id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-person me-1"></i>View Guest Profile
                </a>
                @endif
                @if($booking->room_id)
                <a href="{{ route('admin.rooms.show', $booking->room_id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-door-closed me-1"></i>View Room
                </a>
                @endif
                <hr class="my-1">
                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST"
                      onsubmit="return confirm('Permanently delete this booking?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash me-1"></i>Delete Booking
                    </button>
                </form>
            </div>
        </div>

        {{-- Timestamps --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-clock me-2"></i>Timestamps</div>
            <div class="card-body" style="font-size:0.85rem;">
                <p class="mb-1 text-muted">Created</p>
                <p class="mb-2">{{ $booking->created_at->format('M d, Y H:i') }}</p>
                <p class="mb-1 text-muted">Last Updated</p>
                <p class="mb-0">{{ $booking->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Email Confirmation Modal --}}
<div class="modal fade" id="emailConfirmationModal" tabindex="-1" aria-labelledby="emailConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.bookings.send-confirmation-email', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="emailConfirmationModalLabel">
                        <i class="bi bi-envelope-check me-2"></i>Send Booking Confirmation Email
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        This email will be sent to <strong>{{ $booking->guest_email }}</strong>.
                        You can edit the text below before sending, or
                        <a href="{{ route('admin.email-templates.index') }}" target="_blank">manage the default template</a>.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ $confirmationEmail['subject'] }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Message</label>
                        <textarea name="body" rows="12" class="form-control" required>{{ $confirmationEmail['body'] }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background:#C9A227;">
                        <i class="bi bi-send me-1"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('open_email_modal'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = new bootstrap.Modal(document.getElementById('emailConfirmationModal'));
    modal.show();
});
</script>
@endif

@endsection
