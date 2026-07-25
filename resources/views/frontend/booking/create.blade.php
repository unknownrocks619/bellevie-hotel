@extends('layouts.app')
@section('title', 'Make a Reservation | ' . \App\Models\Setting::get('hotel_name', 'Bellevie Hotel'))

@section('content')

@include('frontend.partials.page-hero', [
    'eyebrow'     => 'SECURE YOUR STAY',
    'title'       => 'Make a Reservation',
    'subtitle'    => 'Complete your booking in minutes — instant confirmation guaranteed.',
    'breadcrumbs' => [
        ['label' => 'Home',  'url' => route('home')],
        ['label' => 'Rooms', 'url' => route('rooms.index')],
        ['label' => 'Reserve'],
    ],
    'minHeight'   => '280px',
])

<div class="container py-5">
    <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
        @csrf
        <input type="hidden" id="room_id" name="room_id" value="{{ old('room_id', request('room', '')) }}">
        <input type="hidden" id="selected_price" value="0">

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-4">

            {{-- ─── LEFT COLUMN ─────────────────────────────────── --}}
            <div class="col-lg-8">

                {{-- STEP 1: Room Selection --}}
                <div class="booking-step mb-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="step-badge">1</div>
                        <div>
                            <h4 class="mb-0">Choose Your Room</h4>
                            <small class="text-muted">Click a room to select it and proceed</small>
                        </div>
                    </div>

                    <div class="row g-3" id="roomGrid">
                        @forelse($rooms ?? [] as $r)
                        <div class="col-md-6 col-xl-4">
                            <div class="room-card-select h-100"
                                 id="card-{{ $r->id }}"
                                 onclick="selectRoom({{ $r->id }}, {{ $r->price_per_night }}, '{{ addslashes($r->name) }}', '{{ $r->featured_image ?? '' }}', '{{ addslashes($r->bed_type ?? '') }}', {{ $r->max_adults }}, {{ $r->show_price ? true : false }})">
                                <div class="room-img-wrap">
                                    <img src="{{ $r->featured_image ?: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&q=60' }}"
                                         alt="{{ $r->name }}"
                                         class="room-img">
                                        @if($r->show_price)
                                            <div class="room-price-badge">${{ number_format($r->price_per_night) }}<span>/night</span></div>
                                        @endif
                                    <div class="room-check-overlay"><i class="bi bi-check-circle-fill"></i></div>
                                </div>
                                <div class="room-card-body">
                                    <h6 class="room-name mb-1">{{ $r->name }}</h6>
                                    <div class="room-meta">
                                        <span><i class="bi bi-tag me-1"></i>{{ $r->roomType->name ?? 'Standard' }}</span>
                                        <span><i class="bi bi-moon me-1"></i>{{ $r->bed_type ?? '—' }}</span>
                                        <span><i class="bi bi-people me-1"></i>{{ $r->max_adults }} guests</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-door-open fs-1 d-block mb-2"></i>
                                No rooms available at this time.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- STEP 2–5: Rest of form (hidden until room selected) --}}
                <div id="bookingDetails" style="display:none;">

                    {{-- STEP 2: Dates --}}
                    <div class="booking-step mb-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="step-badge">2</div>
                            <h4 class="mb-0">Your Stay Dates</h4>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Check-in Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-calendar3" style="color:var(--gold)"></i></span>
                                    <input type="text" name="check_in" id="check_in"
                                           class="form-control @error('check_in') is-invalid @enderror"
                                           placeholder="Select date"
                                           value="{{ old('check_in', request('check_in')) }}"
                                           readonly required>
                                </div>
                                @error('check_in')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Check-out Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-calendar3" style="color:var(--gold)"></i></span>
                                    <input type="text" name="check_out" id="check_out"
                                           class="form-control @error('check_out') is-invalid @enderror"
                                           placeholder="Select date"
                                           value="{{ old('check_out', request('check_out')) }}"
                                           readonly required>
                                </div>
                                @error('check_out')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: Guests --}}
                    <div class="booking-step mb-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="step-badge">3</div>
                            <h4 class="mb-0">Number of Guests</h4>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Adults</label>
                                <select name="adults" class="form-select @error('adults') is-invalid @enderror" onchange="updateSummary()">
                                    @for($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ old('adults', request('adults', 1)) == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'Adult' : 'Adults' }}
                                    </option>
                                    @endfor
                                </select>
                                @error('adults')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Children <span class="text-muted fw-normal">(under 12)</span></label>
                                <select name="children" class="form-select">
                                    @for($i = 0; $i <= 3; $i++)
                                    <option value="{{ $i }}" {{ old('children', request('children', 0)) == $i ? 'selected' : '' }}>
                                        {{ $i == 0 ? 'No children' : $i . ' ' . ($i == 1 ? 'Child' : 'Children') }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 4: Personal Info --}}
                    <div class="booking-step mb-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="step-badge">4</div>
                            <h4 class="mb-0">Your Information</h4>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">First Name</label>
                                <input type="text" name="guest_first_name"
                                       class="form-control @error('guest_first_name') is-invalid @enderror"
                                       value="{{ old('guest_first_name') }}" placeholder="John" required>
                                @error('guest_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" name="guest_last_name"
                                       class="form-control @error('guest_last_name') is-invalid @enderror"
                                       value="{{ old('guest_last_name') }}" placeholder="Doe" required>
                                @error('guest_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="guest_email"
                                       class="form-control @error('guest_email') is-invalid @enderror"
                                       value="{{ old('guest_email') }}" placeholder="john@email.com" required>
                                @error('guest_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="tel" name="guest_phone"
                                       class="form-control @error('guest_phone') is-invalid @enderror"
                                       value="{{ old('guest_phone') }}" placeholder="+1 (555) 000-0000" required>
                                @error('guest_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- STEP 5: Special Requests --}}
                    <div class="booking-step mb-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="step-badge" style="background:#e8e8e8; color:#666;">5</div>
                            <div>
                                <h4 class="mb-0">Special Requests <span class="text-muted fs-6 fw-normal">(optional)</span></h4>
                            </div>
                        </div>
                        <textarea name="special_requests" class="form-control" rows="3"
                                  placeholder="E.g. early check-in, high floor, honeymoon setup, dietary requirements…">{{ old('special_requests') }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex gap-3 align-items-center mt-2">
                        <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>Browse Rooms
                        </a>
                        <button type="submit" class="btn flex-grow-1 py-3 fw-bold" style="background:var(--gold); color:white; border:none; font-size:1.05rem; letter-spacing:0.5px;">
                            <i class="bi bi-shield-check me-2"></i>Confirm Reservation
                        </button>
                    </div>

                </div>{{-- /bookingDetails --}}
            </div>

            {{-- ─── RIGHT SIDEBAR ───────────────────────────────── --}}
            <div class="col-lg-4">
                <div style="position: sticky; top: 90px;">

                    {{-- Empty state --}}
                    <div id="summaryEmpty" class="card border-0 shadow-sm text-center py-5 px-4">
                        <i class="bi bi-building" style="font-size:2.5rem; color:#ddd;"></i>
                        <h6 class="mt-3 mb-1">No room selected yet</h6>
                        <p class="text-muted small mb-0">Choose a room from the grid to see your booking summary here.</p>
                    </div>

                    {{-- Filled state --}}
                    <div id="summaryFilled" style="display:none;">
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <div class="summary-room-img-wrap">
                                <img id="summaryRoomImg" src="" alt="" class="summary-room-img">
                                <div class="summary-room-img-overlay">
                                    <div id="summaryRoomName" class="text-white fw-bold" style="font-size:1.1rem; font-family:'Playfair Display',serif;"></div>
                                    <div id="summaryRoomType" class="text-white-50 small"></div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="p-3 border-bottom">
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Rate per night</small>
                                        <span id="summaryNightRate" class="fw-bold" style="color:var(--gold)">—</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Nights</small>
                                        <span id="summaryNights">—</span>
                                    </div>
                                    <div id="summaryBedRow" class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Bed type</small>
                                        <span id="summaryBed" class="small">—</span>
                                    </div>
                                </div>
                                <div id="summaryTotalBlock" class="p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Estimated Total</span>
                                        <span id="summaryTotal" class="fw-bold fs-5" style="color:var(--gold)">—</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">Taxes & fees included</small>
                                </div>
                            </div>
                        </div>

                        <!-- Change room link -->
                        <div class="text-center mt-3">
                            <button type="button" onclick="clearRoom()" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-arrow-repeat me-1"></i>Change Room
                            </button>
                        </div>

                        <!-- Perks -->
                        <div class="card border-0 shadow-sm mt-3 p-3">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-shield-check" style="color:var(--gold); margin-top:2px;"></i>
                                <small>Free cancellation up to 48h before check-in</small>
                            </div>
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-credit-card" style="color:var(--gold); margin-top:2px;"></i>
                                <small>No payment required today</small>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-headset" style="color:var(--gold); margin-top:2px;"></i>
                                <small>24/7 concierge support</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>{{-- /row --}}
    </form>
</div>

<style>
    /* ── Booking Page Styles ─────────────────────────── */
    .booking-step {
        background: #fff;
        border-radius: 12px;
        padding: 1.75rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }

    .step-badge {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--gold);
        color: #fff;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Room selection cards */
    .room-card-select {
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.2s, transform 0.15s, box-shadow 0.2s;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        user-select: none;
    }
    .room-card-select:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        border-color: rgba(201,162,39,0.4);
    }
    .room-card-select.selected {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(201,162,39,0.18);
    }
    .room-img-wrap {
        position: relative;
        overflow: hidden;
    }
    .room-img {
        width: 100%;
        height: 155px;
        object-fit: cover;
        display: block;
        transition: transform 0.3s;
    }
    .room-card-select:hover .room-img {
        transform: scale(1.04);
    }
    .room-price-badge {
        position: absolute;
        bottom: 8px;
        left: 10px;
        background: rgba(13,27,42,0.82);
        color: #fff;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .room-price-badge span { font-size: 0.7rem; font-weight: 400; }
    .room-check-overlay {
        position: absolute;
        inset: 0;
        background: rgba(201,162,39,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: var(--gold);
        opacity: 0;
        transition: opacity 0.2s;
    }
    .room-card-select.selected .room-check-overlay { opacity: 1; }
    .room-card-body {
        padding: 0.75rem 1rem 1rem;
    }
    .room-name { font-family: 'Playfair Display', serif; }
    .room-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        font-size: 0.75rem;
        color: #777;
    }

    /* Summary sidebar */
    .summary-room-img-wrap {
        position: relative;
        height: 160px;
        overflow: hidden;
    }
    .summary-room-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .summary-room-img-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(13,27,42,0.85) 50%, transparent);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1rem;
    }

    /* Smooth reveal */
    #bookingDetails {
        animation: fadeSlideIn 0.4s ease;
    }
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
let selectedRoomId = null;
let selectedRoomPrice = 0;
let selectedRoomShowPrice = true;
let fpCheckin = null, fpCheckout = null;

document.addEventListener('DOMContentLoaded', function () {
    // Init flatpickr
    fpCheckin = flatpickr('#check_in', {
        minDate: 'today',
        dateFormat: 'Y-m-d',
        disableMobile: true,
        onChange: function(selectedDates) {
            if (fpCheckout) {
                fpCheckout.set('minDate', selectedDates[0] ? new Date(selectedDates[0].getTime() + 86400000) : 'today');
            }
            updateSummary();
        }
    });
    fpCheckout = flatpickr('#check_out', {
        minDate: new Date(new Date().getTime() + 86400000),
        dateFormat: 'Y-m-d',
        disableMobile: true,
        onChange: updateSummary
    });

    // Restore state if validation failed and old data is present
    const oldRoomId = document.getElementById('room_id').value;
    if (oldRoomId) {
        const card = document.getElementById('card-' + oldRoomId);
        if (card) card.click();
    }
});

function selectRoom(id, price, name, img, bed, maxAdults,showPrice = false) {
    selectedRoomId = id;
    selectedRoomPrice = parseFloat(price);
    selectedRoomShowPrice = (showPrice == true);

    // Update hidden fields
    document.getElementById('room_id').value = id;
    document.getElementById('selected_price').value = price;

    // Highlight selected card
    document.querySelectorAll('.room-card-select').forEach(c => c.classList.remove('selected'));
    const card = document.getElementById('card-' + id);
    if (card) card.classList.add('selected');

    // Show rest of form with animation
    const details = document.getElementById('bookingDetails');
    details.style.display = 'block';

    // Update sidebar
    document.getElementById('summaryEmpty').style.display = 'none';
    document.getElementById('summaryFilled').style.display = 'block';
    document.getElementById('summaryRoomName').textContent = name;
    document.getElementById('summaryRoomType').textContent = bed ? '🛏 ' + bed : '';
    document.getElementById('summaryBed').textContent = bed || '—';
    if(showPrice == true) {
        document.getElementById('summaryNightRate').textContent = '$' + parseFloat(price).toFixed(0);
    } else {
        $('#summaryNightRate').parent().remove();
    }

    const totalBlock = document.getElementById('summaryTotalBlock');
    if (totalBlock) totalBlock.style.display = selectedRoomShowPrice ? '' : 'none';

    if (img) document.getElementById('summaryRoomImg').src = img;

    updateSummary();

    // Smooth scroll to form
    setTimeout(() => {
        details.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 100);
}

function clearRoom() {
    selectedRoomId = null;
    selectedRoomPrice = 0;
    selectedRoomShowPrice = true;
    document.getElementById('room_id').value = '';
    document.getElementById('bookingDetails').style.display = 'none';
    document.querySelectorAll('.room-card-select').forEach(c => c.classList.remove('selected'));
    document.getElementById('summaryEmpty').style.display = 'block';
    document.getElementById('summaryFilled').style.display = 'none';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateSummary() {
    if (!selectedRoomPrice) return;

    const ci = document.getElementById('check_in').value;
    const co = document.getElementById('check_out').value;

    if (ci && co) {
        const msPerDay = 86400000;
        const nights = Math.round((new Date(co) - new Date(ci)) / msPerDay);
        if (nights > 0) {
            document.getElementById('summaryNights').textContent = nights + ' night' + (nights > 1 ? 's' : '');
            if (selectedRoomShowPrice) {
                const total = nights * selectedRoomPrice;
                document.getElementById('summaryTotal').textContent = '$' + total.toLocaleString('en-US', { minimumFractionDigits: 0 });
            }
            return;
        }
    }
    document.getElementById('summaryNights').textContent = '—';
    if (selectedRoomShowPrice) {
        document.getElementById('summaryTotal').textContent = '—';
    }
}
</script>

@endsection
