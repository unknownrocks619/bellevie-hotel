@extends('layouts.app')
@section('title', $page->meta_title ?: $page->hero_title)
@section('content')

@include('frontend.partials.page-hero', [
    'eyebrow'     => 'MEETINGS & EVENTS',
    'title'       => $page->hero_title,
    'subtitle'    => $page->hero_subtitle,
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Conference Hall'],
    ],
])

<section style="padding:72px 0;">
    <div class="container">
        <div class="row g-5">
            {{-- Description + details --}}
            <div class="col-lg-7">
                @if($page->description)
                <div style="color:#555;line-height:1.9;font-size:1rem;">{!! $page->description !!}</div>
                @endif
            </div>

            <div class="col-lg-5">
                <div style="background:#f5f0e8;border-radius:12px;padding:28px;">
                    <h5 style="font-family:'Playfair Display',serif;margin-bottom:20px;">Hall Details</h5>

                    @if($page->capacity_text)
                    <div class="d-flex gap-3 mb-3">
                        <i class="bi bi-people" style="color:#C9A227;font-size:1.1rem;"></i>
                        <div>
                            <div style="font-weight:600;font-size:.85rem;">Capacity</div>
                            <div style="color:#666;font-size:.9rem;">{{ $page->capacity_text }}</div>
                        </div>
                    </div>
                    @endif

                    @if($page->layout_text)
                    <div class="d-flex gap-3 mb-3">
                        <i class="bi bi-grid-3x3-gap" style="color:#C9A227;font-size:1.1rem;"></i>
                        <div>
                            <div style="font-weight:600;font-size:.85rem;">Layout Options</div>
                            <div style="color:#666;font-size:.9rem;">{{ $page->layout_text }}</div>
                        </div>
                    </div>
                    @endif

                    <a href="#inquiry-form" class="btn w-100 text-white mt-2" style="background:#C9A227;border:none;">
                        Request This Hall
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Image gallery --}}
@if($galleryImages->isNotEmpty())
<section style="padding:0 0 72px;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-family:'Playfair Display',Georgia,serif;color:#0D1B2A;">Gallery</h2>
        </div>
        <div class="row g-3">
            @foreach($galleryImages as $img)
            <div class="col-6 col-md-4">
                <img src="{{ $img->url_thumb ?: $img->url }}" alt="Conference hall"
                     class="img-fluid w-100" style="aspect-ratio:4/3;object-fit:cover;border-radius:10px;">
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Inquiry form --}}
<section id="inquiry-form" style="padding:72px 0;background:#f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                    <div class="card-body p-4 p-md-5">

                        <h2 style="font-family:'Playfair Display',Georgia,serif;font-size:1.6rem;color:#0D1B2A;margin-bottom:6px;">
                            Request a Conference Booking
                        </h2>
                        <p class="text-muted mb-4" style="font-size:.92rem;">
                            Tell us about your event and our events team will get back to you within 24 hours.
                        </p>

                        @if(session('conference_success'))
                        <div class="d-flex align-items-center gap-2 mb-4 p-3"
                             style="border-left:4px solid #C9A227;background:#fdf8ea;border-radius:6px;color:#5a4500;">
                            <i class="bi bi-check-circle-fill" style="color:#C9A227;font-size:1.1rem;flex-shrink:0;"></i>
                            <div>{{ session('conference_success') }}</div>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('conference.inquiry') }}" method="POST" novalidate>
                            @csrf

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                           class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                           class="form-control @error('email') is-invalid @enderror" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">Phone</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">Company</label>
                                    <input type="text" name="company" value="{{ old('company') }}" class="form-control @error('company') is-invalid @enderror">
                                    @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">Event Date</label>
                                    <input type="date" name="event_date" value="{{ old('event_date') }}" class="form-control @error('event_date') is-invalid @enderror">
                                    @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">Number of Guests</label>
                                    <input type="number" name="guests_count" min="1" value="{{ old('guests_count') }}" class="form-control @error('guests_count') is-invalid @enderror">
                                    @error('guests_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="font-size:.85rem;">Message <span class="text-danger">*</span></label>
                                <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror"
                                          placeholder="Tell us about your event — purpose, preferred layout, catering needs…" required>{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <button type="submit" class="btn w-100 py-3 fw-semibold"
                                    style="background:#C9A227;color:#fff;border:none;border-radius:6px;font-size:1rem;letter-spacing:.04em;">
                                <i class="bi bi-send me-2"></i>Submit Request
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
