@extends('layouts.app')
@section('title', $event->title)
@section('content')

@include('frontend.partials.page-hero', [
    'eyebrow'     => strtoupper($event->type_label),
    'title'       => $event->title,
    'subtitle'    => $event->excerpt ?? '',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Events & Conferences', 'url' => route('events.index')],
        ['label' => $event->title],
    ],
])

<div class="container py-5">
    <div class="row g-5">
        {{-- Main content --}}
        <div class="col-lg-8">
            @if($event->image_url)
            <img src="{{ $event->image_url }}" alt="{{ $event->title }}"
                 style="width:100%;border-radius:12px;margin-bottom:28px;max-height:440px;object-fit:cover;">
            @endif

            <div style="color:#444;line-height:1.9;font-size:1rem;">
                {!! $event->description !!}
            </div>

            @if($event->cta_text && $event->cta_url)
            <div class="mt-4">
                <a href="{{ $event->cta_url }}" class="btn btn-lg text-white" style="background:#C9A227;border:none;padding:12px 34px;">
                    {{ $event->cta_text }}
                </a>
            </div>
            @endif
        </div>

        {{-- Details sidebar --}}
        <div class="col-lg-4">
            <div style="background:#f5f0e8;border-radius:12px;padding:28px;position:sticky;top:100px;">
                <h5 style="font-family:'Playfair Display',serif;margin-bottom:20px;">{{ $event->type_label }} Details</h5>

                @if($event->date_range)
                <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-calendar3" style="color:#C9A227;font-size:1.1rem;"></i>
                    <div>
                        <div style="font-weight:600;font-size:0.85rem;">Date</div>
                        <div style="color:#666;font-size:0.9rem;">{{ $event->date_range }}</div>
                        @if($event->starts_at && $event->starts_at->format('H:i') !== '00:00')
                        <div style="color:#666;font-size:0.85rem;">
                            {{ $event->starts_at->format('g:i A') }}@if($event->ends_at) – {{ $event->ends_at->format('g:i A') }}@endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($event->venue)
                <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-geo-alt" style="color:#C9A227;font-size:1.1rem;"></i>
                    <div>
                        <div style="font-weight:600;font-size:0.85rem;">Venue</div>
                        <div style="color:#666;font-size:0.9rem;">{{ $event->venue }}</div>
                    </div>
                </div>
                @endif

                @if($event->organizer)
                <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-person-badge" style="color:#C9A227;font-size:1.1rem;"></i>
                    <div>
                        <div style="font-weight:600;font-size:0.85rem;">Organizer</div>
                        <div style="color:#666;font-size:0.9rem;">{{ $event->organizer }}</div>
                    </div>
                </div>
                @endif

                @if($event->capacity)
                <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-people" style="color:#C9A227;font-size:1.1rem;"></i>
                    <div>
                        <div style="font-weight:600;font-size:0.85rem;">Capacity</div>
                        <div style="color:#666;font-size:0.9rem;">{{ number_format($event->capacity) }} guests</div>
                    </div>
                </div>
                @endif

                <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-tag" style="color:#C9A227;font-size:1.1rem;"></i>
                    <div>
                        <div style="font-weight:600;font-size:0.85rem;">Price</div>
                        <div style="color:#666;font-size:0.9rem;">
                            {{ $event->price !== null ? '$' . number_format((float) $event->price, 2) : 'Free / On request' }}
                        </div>
                    </div>
                </div>

                @if($event->cta_text && $event->cta_url)
                <a href="{{ $event->cta_url }}" class="btn w-100 text-white mt-2" style="background:#C9A227;border:none;">
                    {{ $event->cta_text }}
                </a>
                @else
                <a href="{{ route('contact') }}" class="btn w-100 text-white mt-2" style="background:#C9A227;border:none;">
                    Enquire Now
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if($related->isNotEmpty())
    <div class="mt-5 pt-4" style="border-top:1px solid #eee;">
        <h4 style="font-family:'Playfair Display',serif;margin-bottom:24px;">More {{ $event->type_label }}s</h4>
        <div class="row g-4">
            @foreach($related as $rel)
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('events.show', $rel) }}" style="text-decoration:none;color:inherit;">
                    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                        @if($rel->image_url)
                        <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}" style="width:100%;height:160px;object-fit:cover;">
                        @else
                        <div style="width:100%;height:160px;background:linear-gradient(135deg,#0D1B2A,#1a3a5c);display:flex;align-items:center;justify-content:center;color:#C9A227;font-size:2rem;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        @endif
                        <div class="p-3">
                            @if($rel->date_range)
                            <div style="color:#C9A227;font-size:0.78rem;font-weight:600;margin-bottom:6px;">{{ $rel->date_range }}</div>
                            @endif
                            <div style="font-weight:600;">{{ Str::limit($rel->title, 55) }}</div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@endsection
