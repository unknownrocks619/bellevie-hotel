@extends('layouts.app')
@section('title', ($seoTitle ?? $page->meta_title ?? $page->title) . ' | ' . \App\Models\Setting::get('hotel_name', 'Bellevie Hotel'))
@section('seo_title', $seoTitle ?? $page->meta_title ?? $page->title)
@section('seo_description', $seoDescription ?? $page->meta_description ?? '')
@if(!empty($seoImage)) @section('seo_image', $seoImage) @endif
@section('content')

<!-- Page Hero -->
<div style="padding-top: 80px; background: linear-gradient(rgba(13,27,42,0.85), rgba(13,27,42,0.85)), url('https://images.unsplash.com/photo-1568495248636-6432b97bd949?auto=format&fit=crop&w=1200&q=60') center/cover; min-height: 200px; display:flex; align-items:center;">
    <div class="container py-5 text-white text-center">
        <h1 class="mb-2">{{ $page->title }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">{{ $page->title }}</li>
            </ol>
        </nav>
    </div>
</div>

@if($page->use_builder && !empty($page->builder_data))
{{-- ── Builder-rendered page ── --}}
@include('frontend.builder-content', ['sections' => $page->builder_data])

@else
{{-- ── Classic content page ── --}}
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    @if($page->featured_image)
                    <img src="{{ $page->featured_image }}" alt="{{ $page->title }}" class="img-fluid rounded mb-4 w-100" style="max-height: 350px; object-fit: cover;">
                    @endif
                    <div class="page-content" style="line-height: 1.9; color: #444;">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">← Back to Home</a>
                <a href="{{ route('contact') }}" class="btn" style="background: var(--gold); color: white; border: none;">Contact Us</a>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.page-content h2, .page-content h3 { color: var(--dark); margin-top: 2rem; margin-bottom: 1rem; }
.page-content p { margin-bottom: 1.2rem; }
.page-content ul, .page-content ol { padding-left: 1.5rem; margin-bottom: 1.2rem; }
.page-content a { color: var(--gold); }
</style>
@endsection
