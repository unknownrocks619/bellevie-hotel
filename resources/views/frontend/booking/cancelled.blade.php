@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <!-- Cancellation Header -->
            <div class="text-center mb-5">
                <div class="mb-4" style="animation: fadeIn 0.6s ease;">
                    <i class="bi bi-x-circle" style="font-size: 80px; color:#FFC107;"></i>
                </div>
                <h1 class="mb-2">Booking Cancelled</h1>
                <p class="text-muted" style="font-size: 1.1rem;">Your reservation has been successfully cancelled</p>
            </div>

            <!-- Booking Reference -->
            <div class="card mb-4 shadow-sm" style="border-left: 5px solid #FFC107;">
                <div class="card-header" style="background:#FFF3CD; border-bottom: none;">
                    <h6 class="mb-0" style="color: #0D1B2A; font-weight: 600;">Cancelled Booking Reference</h6>
                </div>
                <div class="card-body text-center py-4">
                    <div style="font-size: 2rem; font-weight: bold; letter-spacing: 1px; font-family: 'Courier New', monospace;">
                        {{ $booking->booking_reference }}
                    </div>
                    <p class="text-muted mt-3 mb-0">Cancellation processed on {{ \Carbon\Carbon::now()->format('F d, Y') }} at {{ \Carbon\Carbon::now()->format('g:i A') }}</p>
                </div>
            </div>

            <!-- Cancellation Details -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header" style="background:#FFF3CD; border-bottom: 1px solid #ffe69c;">
                    <h6 class="mb-0" style="color: #0D1B2A; font-weight: 600;">Cancelled Booking Details</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Guest Name</strong></p>
                            <h6 class="mb-0">{{ $booking->guest_first_name }} {{ $booking->guest_last_name }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Email</strong></p>
                            <h6 class="mb-0">{{ $booking->guest_email }}</h6>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Room</strong></p>
                            <h6 class="mb-0">{{ $booking->room->name ?? '-' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Room Type</strong></p>
                            <h6 class="mb-0">{{ $booking->room->roomType->name ?? '-' }}</h6>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Original Check-in</strong></p>
                            <h6 class="mb-0">{{ \Carbon\Carbon::parse($booking->check_in)->format('l, F d, Y') }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Original Check-out</strong></p>
                            <h6 class="mb-0">{{ \Carbon\Carbon::parse($booking->check_out)->format('l, F d, Y') }}</h6>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Number of Nights</strong></p>
                            <h6 class="mb-0">{{ $booking->nights ?? 1 }} night{{ isset($booking->nights) && $booking->nights > 1 ? 's' : '' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Original Total</strong></p>
                            <h6 class="mb-0" style="color:#C9A227; font-weight: 700;">${{ number_format($booking->total_amount ?? 0, 2) }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refund Information -->
            <div class="alert alert-warning mb-4" style="border-left: 4px solid #FFC107; background: #FFFBEA;">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; margin-top: 2px; color: #FFC107;"></i>
                    <div>
                        <strong>Refund Information</strong>
                        <p class="mb-0">A refund confirmation email has been sent to <strong>{{ $booking->guest_email }}</strong>. Refunds are typically processed within 5-7 business days. The exact timeline depends on your financial institution.</p>
                        <p class="mb-0 mt-2 small">If you have questions about your refund, please contact our support team or reply to the confirmation email.</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-5 shadow-sm">
                <div class="card-header" style="background:#F5F0E8; border-bottom: 1px solid #ddd;">
                    <h6 class="mb-0" style="color: #0D1B2A; font-weight: 600;">We'd Love to Have You Back</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">We're sorry to see your reservation go. If you'd like to reschedule for another time or have any questions, our team is here to help.</p>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex gap-2 align-items-center mb-2">
                                <i class="bi bi-telephone" style="color:#C9A227; font-size: 1.2rem;"></i>
                                <div>
                                    <small class="text-muted">Phone</small><br>
                                    <strong>+1 (555) 123-4567</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 align-items-center">
                                <i class="bi bi-envelope" style="color:#C9A227; font-size: 1.2rem;"></i>
                                <div>
                                    <small class="text-muted">Email</small><br>
                                    <strong>reservations@belleviehotel.com</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
                <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">Browse Rooms</a>
                <a href="{{ route('booking.create') }}" class="btn" style="background:#C9A227; color:white; border:none;">Book Again</a>
                <a href="{{ route('home') }}" class="btn btn-outline-primary">Back to Home</a>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
@endsection
