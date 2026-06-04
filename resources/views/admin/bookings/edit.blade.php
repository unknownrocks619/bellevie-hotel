@extends('layouts.admin')
@section('page-title', 'Edit Booking')

@section('content')
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-pencil-square" style="font-size:3rem;color:#C9A227;"></i>
        <h4 class="mt-3">Edit Booking Status</h4>
        <p class="text-muted">To update booking status, use the detail page.</p>
        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn text-white me-2" style="background:#C9A227;">
            <i class="bi bi-eye me-1"></i>View Booking #{{ $booking->booking_reference }}
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>
@endsection
