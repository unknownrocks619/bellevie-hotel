@extends('layouts.app')
@section('title', $room->name . ' | ' . \App\Models\Setting::get('hotel_name', 'Bellevie Hotel'))
@section('seo_title', $seoTitle ?? $room->name)
@section('seo_description', $seoDescription ?? $room->description)
@if(!empty($seoImage)) @section('seo_image', $seoImage) @endif
@section('content')

<!-- Room Hero -->
<div style="padding-top: 80px; background: linear-gradient(rgba(13,27,42,0.75), rgba(13,27,42,0.75)), url('{{ $room->featuredImageUrl('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1400&q=60') }}') center/cover; min-height: 280px; display:flex; align-items: flex-end;">
    <div class="container pb-4 text-white">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="background:none; padding:0;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}" class="text-white-50">Rooms</a></li>
                <li class="breadcrumb-item active text-white">{{ $room->name }}</li>
            </ol>
        </nav>
        <h1 class="mb-1" style="font-size:2.5rem;">{{ $room->name }}</h1>
        <div class="d-flex align-items-center gap-3">
            <span class="badge" style="background:var(--gold); font-size:0.85rem;">{{ $room->roomType->name ?? 'Standard' }}</span>
            <span style="color:var(--gold); font-size:1.3rem; font-weight:700;">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($room->price_per_night) }}<span style="font-size:0.85rem; font-weight:400; color:rgba(255,255,255,0.7);"> /night</span></span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-lg-8">
            <!-- Hero Image -->
            <img src="{{ $room->featuredImageUrl('https://via.placeholder.com/800x400') }}" alt="{{ $room->name }}" class="img-fluid rounded mb-4" style="width: 100%; height: 400px; object-fit: cover;">

            <!-- Gallery Thumbnails -->
            @php $galleryImages = $room->galleryImages(); @endphp
            @if($galleryImages->isNotEmpty())
            <div class="row g-2 mb-4">
                @foreach($galleryImages as $galleryImg)
                <div class="col-4">
                    <img src="{{ $galleryImg->url }}" alt="{{ $room->name }}" class="img-fluid rounded" style="height: 150px; object-fit: cover; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#galleryModal" onclick="updateGalleryImage('{{ $galleryImg->url }}')">
                </div>
                @endforeach
            </div>
            @elseif($room->gallery_images && count($room->gallery_images) > 0)
            <div class="row g-2 mb-4">
                @foreach($room->gallery_images as $imageUrl)
                <div class="col-4">
                    <img src="{{ $imageUrl }}" alt="{{ $room->name }}" class="img-fluid rounded" style="height: 150px; object-fit: cover; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#galleryModal" onclick="updateGalleryImage('{{ $imageUrl }}')">
                </div>
                @endforeach
            </div>
            @endif

            <!-- Room Header -->
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <h1 class="mb-0">{{ $room->name }}</h1>
                    <span class="badge bg-light text-dark" style="font-size: 0.9rem;">{{ $room->roomType->name ?? '-' }}</span>
                </div>
                <h5 style="color: #C9A227; margin: 0;">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ $room->price_per_night }}<span style="font-size: 0.8rem; color: #666;">/night</span></h5>
            </div>

            <!-- Info Row -->
            <div class="row mb-4 p-3" style="background: #F5F0E8; border-radius: 0.5rem;">
                <div class="col-md-6 mb-2 mb-md-0">
                    <small class="text-muted">Bed Type</small><br>
                    <strong>{{ $room->bed_type ?? '-' }}</strong>
                </div>
                <div class="col-md-6 mb-2 mb-md-0">
                    <small class="text-muted">Size</small><br>
                    <strong>{{ $room->size_sqft ?? '-' }} sqft</strong>
                </div>
                <div class="col-md-6 mt-2 mt-md-0">
                    <small class="text-muted">Capacity</small><br>
                    <strong>{{ $room->max_adults ?? 2 }} Adults
                        @if($room->max_children > 0)
                        , {{ $room->max_children }} Children
                        @endif
                    </strong>
                </div>
                <div class="col-md-6 mt-2 mt-md-0">
                    <small class="text-muted">View Type</small><br>
                    <strong>{{ $room->view_type ?? 'Standard' }}</strong>
                </div>
            </div>

            <!-- Description Section -->
            <div class="mb-4">
                <h5>Description</h5>
                <p style="color: #666; line-height: 1.8;">{{ $room->description }}</p>
            </div>

            <!-- Amenities Grid -->
            @if($room->amenities && $room->amenities->count() > 0)
            <div class="mb-4">
                <h5>Room Amenities</h5>
                <div class="row">
                    @foreach($room->amenities as $amenity)
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="bi {{ $amenity->icon }}" style="font-size: 20px; color:#C9A227; margin-right: 10px;"></i>
                            <span>{{ $amenity->name }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Similar Rooms Section -->
            @if($similarRooms && $similarRooms->count() > 0)
            <div class="mt-5">
                <h5 class="mb-4">Similar Rooms</h5>
                <div class="row g-3">
                    @foreach($similarRooms as $similar)
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ $similar->featuredImageUrl('https://via.placeholder.com/400x200') }}" class="card-img-top" alt="{{ $similar->name }}" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title">{{ $similar->name }}</h6>
                                <small class="text-muted">{{ $similar->roomType->name ?? '-' }}</small>
                                <p class="card-text mb-2" style="color: #C9A227; font-weight: 600; margin-top: 0.5rem;">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($similar->price_per_night, 2) }}<span style="font-size: 0.85rem; color: #666;">/night</span></p>
                            </div>
                            <div class="card-footer bg-white border-top">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('rooms.show', $similar) }}" class="btn btn-sm btn-outline-primary flex-grow-1">View</a>
                                    <a href="{{ route('booking.create', ['room' => $similar->id]) }}" class="btn btn-sm" style="background: #C9A227; color: white; border: none;">Book</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Booking Widget Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm" style="position: sticky; top: 100px;">
                <div class="card-header" style="background-color:var(--gold); color:white;">
                    <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Reserve This Room</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('booking.create') }}" method="GET">
                        <input type="hidden" name="room" value="{{ $room->id }}">

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 600;">Check-in Date</label>
                            <input type="text" name="check_in" class="form-control datepicker" placeholder="Select date" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 600;">Check-out Date</label>
                            <input type="text" name="check_out" class="form-control datepicker" placeholder="Select date" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 600;">Adults</label>
                            <select name="adults" class="form-select" required>
                                @for($i = 1; $i <= 4; $i++)
                                <option value="{{ $i }}" {{ $i==1?'selected':'' }}>{{ $i }} {{ $i==1?'Adult':'Adults' }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 600;">Children</label>
                            <select name="children" class="form-select">
                                @for($i = 0; $i <= 3; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <button type="submit" class="btn w-100" style="background:#C9A227; color:white; border:none; font-weight: 600; padding: 0.75rem;">Reserve This Room</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img id="modalImage" src="" alt="" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Flatpickr for date pickers
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    flatpickr('.datepicker', {
        minDate: today,
        enableTime: false,
        dateFormat: 'Y-m-d'
    });
});

function updateGalleryImage(src) {
    document.getElementById('modalImage').src = src;
}
</script>
@endsection
