@extends('layouts.app')

@section('content')
@php
    $homeBuilderData = json_decode(\App\Models\Setting::get('home_builder_data', '[]'), true) ?? [];
@endphp

@if(!empty($homeBuilderData))
{{-- ── Builder-rendered home page ── --}}
@include('frontend.builder-content', ['sections' => $homeBuilderData])
@else
{{-- ── Default home page ── --}}
<div class="hero">
    <div class="container">
        <h1 class="animate-on-scroll">{{ $settings['hotel_tagline'] ?? 'Where Luxury Meets Serenity' }}</h1>
        <p class="animate-on-scroll">Experience the pinnacle of luxury hospitality</p>
        <a href="{{ route('booking.create') }}" class="btn" style="background-color: var(--gold); color: white; padding: 0.75rem 2rem; margin-right: 1rem; text-decoration: none;">Reserve Your Stay</a>
        <a href="{{ route('rooms.index') }}" class="btn" style="background-color: transparent; color: white; border: 2px solid white; padding: 0.75rem 2rem; text-decoration: none;">Explore Rooms</a>
    </div>
    <div class="scroll-indicator">
        <i class="bi bi-chevron-down"></i>
    </div>
</div>

<div class="container mt-5 pt-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div style="position: relative; border: 3px solid var(--gold); padding: 1rem;">
                <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=500&q=80" alt="Hotel" class="img-fluid" style="display: block;">
            </div>
        </div>
        <div class="col-lg-7 ps-lg-4">
            <p style="color: var(--gold); font-weight: 600;">ABOUT BELLEVIE</p>
            <h2 class="animate-on-scroll" style="margin-bottom: 1.5rem;">Luxury Redefined</h2>
            <div style="width: 60px; height: 2px; background-color: var(--gold); margin-bottom: 2rem;"></div>
            <p class="animate-on-scroll" style="color: #666; line-height: 1.8; margin-bottom: 2rem;">At Bellevie Hotel, we believe that luxury is more than just elegance—it's the art of making you feel at home in the most extraordinary surroundings. Located in the heart of Beverly Hills, our hotel offers world-class amenities, impeccable service, and unforgettable experiences.</p>
            <div class="row mb-3">
                <div class="col-md-6 text-center">
                    <h3 style="color: var(--gold); margin: 0;">20+</h3>
                    <p style="color: #666;">Years of Excellence</p>
                </div>
                <div class="col-md-6 text-center">
                    <h3 style="color: var(--gold); margin: 0;">50+</h3>
                    <p style="color: #666;">Luxurious Rooms</p>
                </div>
            </div>
            <a href="{{ route('about') }}" class="btn" style="background-color: var(--gold); color: white; border: none; padding: 0.75rem 1.5rem; text-decoration: none; display: inline-block;">Learn More</a>
        </div>
    </div>
</div>

<section style="padding: 5rem 0; background-color: var(--cream);">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 3rem;">Featured Rooms & Suites</h2>
        <div class="row">
            @foreach($featuredRooms as $room)
            <div class="col-lg-4 mb-4 animate-on-scroll">
                <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <img src="{{ $room->featuredImageUrl('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=400&q=80') }}" alt="{{ $room->name }}" class="card-img-top" style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <span class="badge bg-light text-dark mb-2">{{ $room->roomType->name }}</span>
                        <h5 class="card-title">{{ $room->name }}</h5>
                        <p class="card-text" style="color: #666; font-size: 0.9rem;">{{ Str::limit($room->description, 60) }}</p>
                        <div class="row text-center mb-3" style="font-size: 0.85rem; color: #666;">
                            <div class="col-4">
                                <i class="bi bi-rulers"></i> {{ $room->size_sqft }}ft²
                            </div>
                            <div class="col-4">
                                <i class="bi bi-people"></i> {{ $room->max_adults }} Guests
                            </div>
                            <div class="col-4">
                                <i class="bi bi-door-closed"></i> {{ $room->bed_type }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="color: var(--gold); font-weight: 700; font-size: 1.2rem;">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ $room->price_per_night }}</span>
                            <a href="{{ route('rooms.show', $room) }}" class="btn btn-sm" style="background-color: var(--gold); color: white; border: none; text-decoration: none;">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('rooms.index') }}" class="btn" style="background-color: var(--gold); color: white; border: none; padding: 0.75rem 2rem; text-decoration: none;">View All Rooms</a>
        </div>
    </div>
</section>

<section style="padding: 5rem 0;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 3rem;">Why Choose Bellevie?</h2>
        <div class="row">
            <div class="col-md-6 col-lg-3 text-center mb-4 animate-on-scroll">
                <i class="bi bi-gem" style="font-size: 2.5rem; color: var(--gold);"></i>
                <h5 class="mt-3">World-Class Amenities</h5>
                <p style="color: #666;">Experience luxury like never before</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center mb-4 animate-on-scroll">
                <i class="bi bi-people-fill" style="font-size: 2.5rem; color: var(--gold);"></i>
                <h5 class="mt-3">Exceptional Service</h5>
                <p style="color: #666;">Attentive staff available 24/7</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center mb-4 animate-on-scroll">
                <i class="bi bi-geo-alt-fill" style="font-size: 2.5rem; color: var(--gold);"></i>
                <h5 class="mt-3">Prime Location</h5>
                <p style="color: #666;">Heart of Beverly Hills</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center mb-4 animate-on-scroll">
                <i class="bi bi-heart-fill" style="font-size: 2.5rem; color: var(--gold);"></i>
                <h5 class="mt-3">Memorable Experiences</h5>
                <p style="color: #666;">Create unforgettable moments</p>
            </div>
        </div>
    </div>
</section>

<!-- Venues & Services Section -->
<section style="padding: 5rem 0; background-color: var(--cream);">
    <div class="container">
        <div class="text-center mb-5">
            <p style="color: var(--gold); font-weight: 600; letter-spacing: 2px; text-transform: uppercase;">Beyond Accommodation</p>
            <h2>Venues & Experiences</h2>
            <div style="width: 60px; height: 2px; background: var(--gold); margin: 1rem auto;"></div>
        </div>
        <div class="row g-4">
            <!-- Conference & Meetings -->
            <div class="col-lg-4 animate-on-scroll">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80"
                         alt="Conference" class="card-img-top" style="height:220px;object-fit:cover;">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="bi bi-building" style="font-size:2rem;color:var(--gold);"></i>
                        </div>
                        <h5>Conference & Meetings</h5>
                        <p class="text-muted" style="font-size:.9rem;">State-of-the-art meeting facilities for corporate events, conferences, and business gatherings. Capacity up to 300 guests with full AV support.</p>
                        <ul class="list-unstyled" style="font-size:.85rem; color:#666;">
                            <li><i class="bi bi-check-circle text-success me-2"></i>5 versatile meeting rooms</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>High-speed fiber WiFi</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Catering & coffee breaks</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Projectors & smart screens</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-0 p-4 pt-0">
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-sm">Enquire Now</a>
                    </div>
                </div>
            </div>
            <!-- Events & Celebrations -->
            <div class="col-lg-4 animate-on-scroll">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=600&q=80"
                         alt="Events" class="card-img-top" style="height:220px;object-fit:cover;">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="bi bi-stars" style="font-size:2rem;color:var(--gold);"></i>
                        </div>
                        <h5>Events & Celebrations</h5>
                        <p class="text-muted" style="font-size:.9rem;">From intimate gatherings to grand galas, our expert event team transforms your vision into unforgettable moments.</p>
                        <ul class="list-unstyled" style="font-size:.85rem; color:#666;">
                            <li><i class="bi bi-check-circle text-success me-2"></i>Weddings & receptions</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Birthday & anniversary parties</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Gala dinners & award ceremonies</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Dedicated event coordinator</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-0 p-4 pt-0">
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-sm">Plan Your Event</a>
                    </div>
                </div>
            </div>
            <!-- Bar & Restaurant -->
            <div class="col-lg-4 animate-on-scroll">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=600&q=80"
                         alt="Restaurant" class="card-img-top" style="height:220px;object-fit:cover;">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="bi bi-cup-hot-fill" style="font-size:2rem;color:var(--gold);"></i>
                        </div>
                        <h5>Dining & Bar</h5>
                        <p class="text-muted" style="font-size:.9rem;">Indulge in world-class cuisine at Le Bellevie Restaurant or unwind with handcrafted cocktails at the Rooftop Gold Bar.</p>
                        <ul class="list-unstyled" style="font-size:.85rem; color:#666;">
                            <li><i class="bi bi-check-circle text-success me-2"></i>Farm-to-table fine dining</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Award-winning wine cellar</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Rooftop bar with city views</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Private dining available</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-0 p-4 pt-0">
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-sm">Make a Reservation</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section style="padding: 5rem 0; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1596178065887-ba9ecb47b8b5?auto=format&fit=crop&w=1200&q=80') center/cover; color: white;">
    <div class="container text-center">
        <h2 class="mb-4">Ready for an Extraordinary Experience?</h2>
        <p style="font-size: 1.1rem; margin-bottom: 2rem;">Book your dream stay at Bellevie Hotel today</p>
        <a href="{{ route('booking.create') }}" class="btn" style="background-color: var(--gold); color: white; border: none; padding: 0.75rem 2rem; text-decoration: none; display: inline-block;">Reserve Now</a>
    </div>
</section>
@endif
@endsection
