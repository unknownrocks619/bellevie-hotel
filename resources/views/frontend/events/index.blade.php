@extends('layouts.app')
@section('title', 'Events & Conferences')
@section('content')

@include('frontend.partials.page-hero', [
    'eyebrow'     => 'WHAT\'S HAPPENING',
    'title'       => 'Events & Conferences',
    'subtitle'    => 'Discover upcoming events, meetings and conferences at Bellevie Hotel',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Events & Conferences'],
    ],
])

<div class="container py-5">
    {{-- Type filter --}}
    <div class="d-flex gap-2 flex-wrap mb-4">
        <a href="{{ route('events.index') }}"
           class="btn btn-sm btn-outline-secondary"
           style="{{ !$type ? 'background:#C9A227;color:#fff;border:none;' : '' }}">All</a>
        @foreach($types as $slug => $label)
        <a href="{{ route('events.index', ['type' => $slug]) }}"
           class="btn btn-sm btn-outline-secondary"
           style="{{ $type === $slug ? 'background:#C9A227;color:#fff;border:none;' : '' }}">{{ $label }}s</a>
        @endforeach
    </div>

    <div class="row g-4">
        @forelse($events as $event)
        <div class="col-sm-6 col-lg-4">
            <div class="h-100" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);display:flex;flex-direction:column;">
                <a href="{{ route('events.show', $event) }}" style="display:block;position:relative;">
                    @if($event->image_url)
                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}" style="width:100%;height:210px;object-fit:cover;">
                    @else
                    <div style="width:100%;height:210px;background:linear-gradient(135deg,#0D1B2A,#1a3a5c);display:flex;align-items:center;justify-content:center;color:#C9A227;font-size:2.5rem;">
                        <i class="bi {{ $event->type === 'conference' ? 'bi-people' : 'bi-calendar-event' }}"></i>
                    </div>
                    @endif
                    <span style="position:absolute;top:12px;left:12px;background:#C9A227;color:#fff;padding:4px 12px;border-radius:50px;font-size:0.72rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;">
                        {{ $event->type_label }}
                    </span>
                </a>
                <div class="p-4" style="flex:1;display:flex;flex-direction:column;">
                    @if($event->date_range)
                    <div style="color:#C9A227;font-size:0.82rem;font-weight:600;margin-bottom:8px;">
                        <i class="bi bi-calendar3 me-1"></i>{{ $event->date_range }}
                    </div>
                    @endif
                    <h5 style="font-family:'Playfair Display',serif;margin-bottom:10px;">
                        <a href="{{ route('events.show', $event) }}" style="color:#0D1B2A;text-decoration:none;">{{ $event->title }}</a>
                    </h5>
                    <p style="color:#666;font-size:0.9rem;line-height:1.6;flex:1;">
                        {{ Str::limit($event->excerpt ?: strip_tags($event->description), 110) }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        @if($event->venue)
                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($event->venue, 26) }}</small>
                        @else
                        <span></span>
                        @endif
                        <a href="{{ route('events.show', $event) }}" style="color:#C9A227;font-size:0.85rem;font-weight:600;text-decoration:none;">
                            Details <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-calendar-x" style="font-size:2.5rem;display:block;margin-bottom:12px;color:#C9A227;"></i>
            <p>No {{ $type ? strtolower($types[$type]) . 's' : 'events or conferences' }} scheduled at the moment. Please check back soon.</p>
        </div>
        @endforelse
    </div>

    @if($events->hasPages())
    <div class="mt-5 d-flex justify-content-center">
        {{ $events->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
