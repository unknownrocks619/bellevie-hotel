@extends('layouts.admin')
@section('page-title', 'New Booking')

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('admin.bookings.calendar') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Calendar
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list-ul me-1"></i>All Bookings
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Create New Booking</div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.store') }}" method="POST">
                    @csrf

                    <h6 class="mb-3 pb-2 border-bottom" style="color:#C9A227;">Room & Dates</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                            <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required onchange="updatePrice(this)">
                                <option value="">— Select a Room —</option>
                                @foreach($rooms as $room)
                                <option value="{{ $room->id }}"
                                        data-price="{{ $room->price_per_night }}"
                                        {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }} — {{ $room->roomType->name ?? '' }} — ${{ number_format($room->price_per_night) }}/night
                                </option>
                                @endforeach
                            </select>
                            @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Check-in Date <span class="text-danger">*</span></label>
                            <input type="text" name="check_in" id="check_in" class="form-control datepicker @error('check_in') is-invalid @enderror"
                                   value="{{ old('check_in', $date) }}" required>
                            @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Check-out Date <span class="text-danger">*</span></label>
                            <input type="text" name="check_out" id="check_out" class="form-control datepicker @error('check_out') is-invalid @enderror"
                                   value="{{ old('check_out') }}" required>
                            @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Adults <span class="text-danger">*</span></label>
                            <select name="adults" class="form-select @error('adults') is-invalid @enderror" required>
                                @for($i=1;$i<=6;$i++)
                                <option value="{{ $i }}" {{ old('adults',1)==$i?'selected':'' }}>{{ $i }} {{ $i==1?'Adult':'Adults' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Children</label>
                            <select name="children" class="form-select">
                                @for($i=0;$i<=4;$i++)
                                <option value="{{ $i }}" {{ old('children',0)==$i?'selected':'' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rate/Night</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" id="displayRate" class="form-control" value="—" readonly>
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-3 pb-2 border-bottom" style="color:#C9A227;">Guest Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="guest_first_name" class="form-control @error('guest_first_name') is-invalid @enderror"
                                   value="{{ old('guest_first_name') }}" required>
                            @error('guest_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="guest_last_name" class="form-control @error('guest_last_name') is-invalid @enderror"
                                   value="{{ old('guest_last_name') }}" required>
                            @error('guest_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="guest_email" class="form-control @error('guest_email') is-invalid @enderror"
                                   value="{{ old('guest_email') }}" required>
                            @error('guest_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="guest_phone" class="form-control @error('guest_phone') is-invalid @enderror"
                                   value="{{ old('guest_phone') }}" required>
                            @error('guest_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="mb-3 pb-2 border-bottom" style="color:#C9A227;">Booking Status</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="confirmed" {{ old('status','confirmed')=='confirmed'?'selected':'' }}>Confirmed</option>
                                <option value="pending" {{ old('status')=='pending'?'selected':'' }}>Pending</option>
                                <option value="checked_in" {{ old('status')=='checked_in'?'selected':'' }}>Checked In</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Status</label>
                            <select name="payment_status" class="form-select">
                                <option value="pending" {{ old('payment_status','pending')=='pending'?'selected':'' }}>Pending</option>
                                <option value="paid" {{ old('payment_status')=='paid'?'selected':'' }}>Paid</option>
                                <option value="partial" {{ old('payment_status')=='partial'?'selected':'' }}>Partial</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Special Requests</label>
                            <textarea name="special_requests" class="form-control" rows="2" placeholder="Guest's special requests…">{{ old('special_requests') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Internal Notes</label>
                            <textarea name="internal_notes" class="form-control" rows="2" placeholder="Staff notes (not visible to guest)…">{{ old('internal_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn" style="background:#C9A227;color:white;border:none;">
                            <i class="bi bi-calendar-plus me-1"></i>Create Booking
                        </button>
                        <a href="{{ route('admin.bookings.calendar') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary sidebar -->
    <div class="col-lg-4">
        <div class="card" style="position:sticky; top:80px;">
            <div class="card-header" style="background:#C9A227; color:white;">
                <i class="bi bi-receipt me-2"></i>Booking Summary
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Room</span>
                    <span id="sumRoom" class="fw-semibold">—</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Rate/night</span>
                    <span id="sumRate">—</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Nights</span>
                    <span id="sumNights">—</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total</span>
                    <span id="sumTotal" class="fw-bold fs-5" style="color:#C9A227;">—</span>
                </div>
                <small class="text-muted d-block mt-1">Estimated total before taxes</small>
            </div>
        </div>
    </div>
</div>

<script>
let currentRate = 0;

function updatePrice(sel) {
    const opt = sel.options[sel.selectedIndex];
    currentRate = parseFloat(opt.dataset.price || 0);
    document.getElementById('displayRate').value = currentRate ? '$' + currentRate.toFixed(0) : '—';
    document.getElementById('sumRoom').textContent = opt.value ? opt.text.split(' — ')[0] : '—';
    document.getElementById('sumRate').textContent = currentRate ? '$' + currentRate.toFixed(0) + '/night' : '—';
    calcTotal();
}

function calcTotal() {
    const ci = document.getElementById('check_in').value;
    const co = document.getElementById('check_out').value;
    if (ci && co && currentRate) {
        const nights = Math.round((new Date(co) - new Date(ci)) / 86400000);
        if (nights > 0) {
            document.getElementById('sumNights').textContent = nights + ' night' + (nights > 1 ? 's' : '');
            document.getElementById('sumTotal').textContent = '$' + (nights * currentRate).toLocaleString();
            return;
        }
    }
    document.getElementById('sumNights').textContent = '—';
    document.getElementById('sumTotal').textContent = '—';
}

document.addEventListener('DOMContentLoaded', function() {
    // Re-init flatpickr with onChange to update summary
    flatpickr('#check_in', { dateFormat: 'Y-m-d', onChange: calcTotal });
    flatpickr('#check_out', { dateFormat: 'Y-m-d', onChange: calcTotal });
});
</script>
@endsection
