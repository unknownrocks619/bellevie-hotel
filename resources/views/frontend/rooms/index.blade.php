@extends('layouts.app')
@section('content')
<div class="container-fluid py-5" style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('https://images.unsplash.com/photo-1578683078519-94d5e5bba5c1?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    <div class="container">
        <h1 class="text-white mb-2">Our Rooms</h1>
        <p class="text-white-50">Discover luxury accommodations at Bellevie Hotel</p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card mb-4" style="position: sticky; top: 20px;">
                <div class="card-header" style="background-color:#C9A227; color:white;">
                    <strong>Filter Rooms</strong>
                </div>
                <div class="card-body">
                    <form method="GET">
                        <div class="mb-3">
                            <label class="form-label">Room Type</label>
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                @if(isset($roomTypes))
                                @foreach($roomTypes as $type)
                                <option value="{{ $type->slug }}" {{ request('type')==$type->slug?'selected':'' }}>{{ $type->name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Max Price: $<span id="priceValue">500</span></label>
                            <input type="range" name="price" class="form-range" min="0" max="500" value="{{ request('price', 500) }}" id="priceRange" onchange="document.getElementById('priceValue').textContent = this.value; this.form.submit()">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Max Adults</label>
                            <select name="adults" class="form-select" onchange="this.form.submit()">
                                <option value="">Any</option>
                                @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ request('adults')==$i?'selected':'' }}>{{ $i }} Adult{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>

                        <button class="btn btn-outline-secondary btn-sm w-100">Reset Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @forelse($rooms as $room)
            <div class="card mb-4 shadow-sm">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ $room->featured_image ?? 'https://via.placeholder.com/400x300' }}" class="img-fluid rounded-start" alt="{{ $room->name }}" style="height: 250px; object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title">{{ $room->name }}</h5>
                                    <small class="text-muted">{{ $room->roomType->name ?? '-' }}</small>
                                </div>
                                <div class="text-end">
                                    <h6 style="color:#C9A227;" class="mb-0">${{ number_format($room->price_per_night, 2) }}</h6>
                                    <small class="text-muted">per night</small>
                                </div>
                            </div>

                            <p class="card-text text-muted">{{ Str::limit($room->description, 100) }}</p>

                            <div class="mb-3">
                                <div class="d-flex gap-3">
                                    <div>
                                        <i class="bi bi-people" style="color:#C9A227;"></i>
                                        <small>{{ $room->max_adults ?? 2 }} Adults</small>
                                    </div>
                                    <div>
                                        <i class="bi bi-rulers" style="color:#C9A227;"></i>
                                        <small>{{ $room->size_sqft ?? '-' }} sqft</small>
                                    </div>
                                    <div>
                                        <i class="bi bi-bed" style="color:#C9A227;"></i>
                                        <small>{{ $room->bed_type ?? 'Bed' }}</small>
                                    </div>
                                </div>
                            </div>

                            @if($room->amenities && $room->amenities->count() > 0)
                            <div class="mb-3">
                                <small class="text-muted"><strong>Amenities:</strong></small>
                                <div class="mt-1">
                                    @foreach($room->amenities->take(3) as $amenity)
                                    <span class="badge bg-light text-dark me-1">
                                        <i class="bi {{ $amenity->icon }}"></i> {{ $amenity->name }}
                                    </span>
                                    @endforeach
                                    @if($room->amenities->count() > 3)
                                    <span class="badge bg-light text-dark">+{{ $room->amenities->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="d-flex gap-2 mt-4">
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-outline-primary">View Details</a>
                                <a href="{{ route('booking.create', ['room' => $room->id]) }}" class="btn btn-primary" style="background:#C9A227;border:none;">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No rooms available matching your filters.
            </div>
            @endforelse

            @if($rooms->hasPages())
            <div class="d-flex justify-content-center">
                {{ $rooms->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
