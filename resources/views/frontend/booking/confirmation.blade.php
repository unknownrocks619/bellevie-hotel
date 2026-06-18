@extends('layouts.app')
@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <!-- Success Header -->
                <div class="text-center mb-5">
                    <div class="mb-4" style="animation: scaleIn 0.6s ease;">
                        <i class="bi bi-check-circle" style="font-size: 80px; color:#28a745;"></i>
                    </div>
                    <h1 class="mb-2">Booking Enquiry Sent</h1>
                    <p class="text-muted" style="font-size: 1.1rem;">Thank you for choosing Bellevie Hotel</p>
                </div>

                <!-- Booking Reference -->
                <div class="card mb-4 shadow-sm" style="border-left: 5px solid #C9A227;">
                    <div class="card-header" style="background:#F5F0E8; border-bottom: none;">
                        <h6 class="mb-0" style="color: #0D1B2A; font-weight: 600;">Your Enquiry Reference</h6>
                    </div>
                    <div class="card-body text-center py-4">
                        <div
                            style="font-size: 2.5rem; font-weight: bold; color:#C9A227; letter-spacing: 2px; font-family: 'Courier New', monospace;">
                            {{ $booking->booking_reference }}
                        </div>
                        <p class="text-muted mt-3 mb-0">Please save this reference number for your records</p>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header" style="background:#F5F0E8; border-bottom: 1px solid #ddd;">
                        <h6 class="mb-0" style="color: #0D1B2A; font-weight: 600;">Booking Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><strong>Room</strong></p>
                                <h6 class="mb-0">{{ $booking->room->name ?? '-' }}</h6>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><strong>Guest Name</strong></p>
                                <h6 class="mb-0">{{ $booking->guest_first_name }} {{ $booking->guest_last_name }}</h6>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><strong>Check-in</strong></p>
                                <h6 class="mb-0">{{ \Carbon\Carbon::parse($booking->check_in)->format('l, F d, Y') }}</h6>
                                <small class="text-muted">After 3:00 PM</small>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><strong>Check-out</strong></p>
                                <h6 class="mb-0">{{ \Carbon\Carbon::parse($booking->check_out)->format('l, F d, Y') }}
                                </h6>
                                <small class="text-muted">Before 11:00 AM</small>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><strong>Number of Nights</strong></p>
                                <h6 class="mb-0">{{ $booking->nights ?? 1 }}
                                    night{{ isset($booking->nights) && $booking->nights > 1 ? 's' : '' }}</h6>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><strong>Guests</strong></p>
                                <h6 class="mb-0">{{ $booking->adults }} Adult{{ $booking->adults > 1 ? 's' : '' }}
                                    @if ($booking->children > 0)
                                        , {{ $booking->children }} Child{{ $booking->children > 1 ? 'ren' : '' }}
                                    @endif
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Summary -->
                {{-- <div class="card mb-4 shadow-sm" style="background:#F5F0E8; border: none; border-left: 5px solid #C9A227;">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="text-muted mb-0">Nightly Rate</p>
                            </div>
                            <div class="col-6 text-end">
                                <strong>${{ number_format($booking->room->price_per_night ?? 0, 2) }}</strong>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="text-muted mb-0">{{ $booking->nights ?? 1 }}
                                    Night{{ isset($booking->nights) && $booking->nights > 1 ? 's' : '' }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <strong>${{ number_format($booking->subtotal ?? 0, 2) }}</strong>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="text-muted mb-0">Tax (10%)</p>
                            </div>
                            <div class="col-6 text-end">
                                <strong>${{ number_format($booking->tax_amount ?? 0, 2) }}</strong>
                            </div>
                        </div>

                        <hr style="border-top: 2px solid #C9A227;">

                        <div class="row">
                            <div class="col-6">
                                <p class="mb-0" style="font-weight: 700; font-size: 1.1rem;">Total Amount</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-0" style="color:#C9A227; font-size: 1.5rem; font-weight: 700;">
                                    ${{ number_format($booking->total_amount ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Guest Email Confirmation -->
                {{-- <div class="alert alert-info mb-4" style="border-left: 4px solid #0dcaf0;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle" style="font-size: 1.2rem; margin-top: 2px;"></i>
                        <div>
                            <strong>Confirmation Email Sent</strong>
                            <p class="mb-0">A confirmation email has been sent to
                                <strong>{{ $booking->guest_email }}</strong>. Please check your inbox (and spam folder) for
                                booking details.</p>
                        </div>
                    </div>
                </div> --}}

                <!-- Special Requests -->
                @if ($booking->special_requests)
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header" style="background:#F5F0E8; border-bottom: 1px solid #ddd;">
                            <h6 class="mb-0" style="color: #0D1B2A; font-weight: 600;">Special Requests</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $booking->special_requests }}</p>
                        </div>
                    </div>
                @endif

                <!-- What's Next -->
                <div class="card mb-5 shadow-sm" style="border-top: 3px solid #C9A227;">
                    <div class="card-header" style="background:#F5F0E8; border-bottom: 1px solid #ddd;">
                        <h6 class="mb-0" style="color: #0D1B2A; font-weight: 600;">What's Next?</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle" style="color:#28a745; margin-right: 10px;"></i>
                                We'll send you a confirmation email with all booking details
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle" style="color:#28a745; margin-right: 10px;"></i>
                                You can manage your booking using your reference number
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle" style="color:#28a745; margin-right: 10px;"></i>
                                Check-in is available after 3:00 PM - contact us if you need early arrival
                            </li>
                            <li class="mb-0">
                                <i class="bi bi-check-circle" style="color:#28a745; margin-right: 10px;"></i>
                                Feel free to contact us if you have any questions
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-center mb-3">
                    <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">View All Rooms</a>
                    <a href="{{ route('home') }}" class="btn"
                        style="background:#C9A227; color:white; border:none;">Return to Home</a>
                </div>

                <!-- Cancellation Link -->
                @if ($booking->status === 'pending')
                    <div class="text-center pt-3" style="border-top: 1px solid #ddd;">
                        <small class="text-muted">Need to cancel? </small>
                        <a href="{{ route('booking.cancel', ['booking' => $booking->id, 'token' => $booking->cancellation_token ?? '']) }}"
                            class="text-danger text-decoration-none" style="font-size: 0.9rem;">
                            <strong>Cancel this booking</strong>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
@endsection
