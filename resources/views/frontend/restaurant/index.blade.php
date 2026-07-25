@extends('layouts.app')
@section('title', $page->meta_title ?: $page->hero_title)
@section('content')

@include('frontend.partials.page-hero', [
    'eyebrow'     => 'DINING',
    'title'       => $page->hero_title,
    'subtitle'    => $page->hero_subtitle,
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Restaurant'],
    ],
])

{{-- Intro / description --}}
<section style="padding:72px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                @if($page->intro_title)
                <h2 style="font-family:'Playfair Display',Georgia,serif;color:#0D1B2A;margin-bottom:18px;">
                    {{ $page->intro_title }}
                </h2>
                @endif
                @if($page->description)
                <div style="color:#555;line-height:1.9;font-size:1rem;">{!! $page->description !!}</div>
                @endif
                @if($page->opening_hours)
                <div class="mt-4 d-inline-block text-start" style="background:#f5f0e8;border-radius:10px;padding:20px 28px;">
                    <div style="color:#C9A227;font-weight:700;font-size:.75rem;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px;">
                        <i class="bi bi-clock me-1"></i>Opening Hours
                    </div>
                    <div style="white-space:pre-line;color:#333;font-size:.9rem;line-height:1.7;">{{ $page->opening_hours }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Featured menu --}}
@if($featuredItems->isNotEmpty())
<section style="padding:0 0 72px;background:#f8f9fa;padding-top:56px;">
    <div class="container">
        <div class="text-center mb-5">
            <p style="color:#C9A227;font-size:.75rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;margin-bottom:8px;">
                Chef's Selection
            </p>
            <h2 style="font-family:'Playfair Display',Georgia,serif;color:#0D1B2A;">Featured Menu</h2>
        </div>
        <div class="row g-4">
            @foreach($featuredItems as $item)
            <div class="col-sm-6 col-lg-4">
                <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);height:100%;">
                    @if($item->image_url)
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width:100%;height:190px;object-fit:cover;">
                    @else
                    <div style="width:100%;height:190px;background:linear-gradient(135deg,#0D1B2A,#1a3a5c);display:flex;align-items:center;justify-content:center;color:#C9A227;font-size:2rem;">
                        <i class="bi bi-egg-fried"></i>
                    </div>
                    @endif
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div style="font-weight:600;color:#0D1B2A;">{{ $item->name }}</div>
                            @if($item->show_price && $item->price !== null)
                            <div style="color:#C9A227;font-weight:700;white-space:nowrap;">${{ number_format((float) $item->price, 2) }}</div>
                            @endif
                        </div>
                        @if($item->description)
                        <p class="text-muted mb-0 mt-1" style="font-size:.85rem;">{{ Str::limit($item->description, 80) }}</p>
                        @endif
                        @if(!empty($item->dietary_tags_list))
                        <div class="mt-2 d-flex flex-wrap gap-1">
                            @foreach($item->dietary_tags_list as $tag)
                            <span class="badge rounded-pill" style="background:#C9A22720;color:#C9A227;border:1px solid #C9A22740;font-size:.68rem;">{{ $tag }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Full menu by category --}}
<section style="padding:72px 0;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-family:'Playfair Display',Georgia,serif;color:#0D1B2A;">Our Menu</h2>
        </div>

        @forelse($categories as $category)
        <div class="mb-5">
            <h3 style="font-family:'Playfair Display',Georgia,serif;color:#0D1B2A;border-bottom:2px solid #C9A227;padding-bottom:10px;margin-bottom:24px;display:inline-block;">
                {{ $category->name }}
            </h3>
            @if($category->description)
            <p class="text-muted mb-4" style="font-size:.9rem;">{{ $category->description }}</p>
            @endif

            <div class="row g-4">
                @foreach($category->items as $item)
                <div class="col-md-6">
                    <div class="d-flex gap-3 align-items-start">
                        @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                             style="width:76px;height:76px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                        @else
                        <div style="width:76px;height:76px;border-radius:8px;flex-shrink:0;background:#C9A22715;display:flex;align-items:center;justify-content:center;color:#C9A227;">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                        @endif
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div style="font-weight:600;color:#0D1B2A;">{{ $item->name }}</div>
                                @if($item->show_price && $item->price !== null)
                                <div style="color:#C9A227;font-weight:700;white-space:nowrap;">${{ number_format((float) $item->price, 2) }}</div>
                                @endif
                            </div>
                            @if($item->description)
                            <p class="text-muted mb-1" style="font-size:.85rem;">{{ $item->description }}</p>
                            @endif
                            @if(!empty($item->dietary_tags_list))
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($item->dietary_tags_list as $tag)
                                <span class="badge rounded-pill" style="background:#C9A22720;color:#C9A227;border:1px solid #C9A22740;font-size:.68rem;">{{ $tag }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <p class="text-center text-muted">The menu is being updated — please check back soon.</p>
        @endforelse
    </div>
</section>

{{-- CTA --}}
<section style="padding:56px 0;background:linear-gradient(160deg,#0D1B2A 0%,#1a3a5c 100%);text-align:center;">
    <div class="container">
        <h3 style="font-family:'Playfair Display',Georgia,serif;color:#fff;margin-bottom:14px;">Planning a special dinner?</h3>
        <p style="color:rgba(255,255,255,.6);margin-bottom:24px;">Reach out and our team will help you reserve a table or arrange a private dining experience.</p>
        <a href="{{ route('contact') }}" class="btn btn-lg text-white" style="background:#C9A227;border:none;padding:12px 34px;">Contact Us</a>
    </div>
</section>

@endsection
