@extends('layouts.app')
@section('title', ($post->meta_title ?: $post->title) . ' | ' . \App\Models\Setting::get('hotel_name', 'Bellevie Hotel'))
@section('seo_title', $seoTitle ?? $post->title)
@section('seo_description', $seoDescription ?? $post->excerpt)
@if(!empty($seoImage)) @section('seo_image', $seoImage) @endif
@section('content')
<div class="container-fluid py-5 mb-4" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $post->featuredImageUrl('https://via.placeholder.com/1920x400') }}'); background-size: cover; background-position: center;">
    <div class="container text-white">
        <div class="mb-3">
            @if($post->category)
            <span class="badge" style="background:#C9A227;">{{ $post->category->name }}</span>
            @endif
            @if($post->is_featured)
            <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Featured</span>
            @endif
        </div>
        <h1>{{ $post->title }}</h1>
        <p class="text-white-50">Published on {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('F d, Y') : 'N/A' }}</p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <article>
                {!! nl2br(e($post->content)) !!}
            </article>

            <hr class="my-5">

            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary">← Back to Blog</a>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="bi bi-share2"></i> Share: 
                        <a href="#" class="text-decoration-none">Facebook</a> | 
                        <a href="#" class="text-decoration-none">Twitter</a>
                    </small>
                </div>
            </div>

            @if($relatedPosts && $relatedPosts->count() > 0)
            <h5 class="mb-4">Related Posts</h5>
            <div class="row g-3">
                @foreach($relatedPosts as $related)
                <div class="col-md-6">
                    <div class="card h-100">
                        @php $relatedImg = $related->featuredImageUrl($related->featured_image ?? ''); @endphp
                        @if($relatedImg)
                        <img src="{{ $relatedImg }}" class="card-img-top" alt="{{ $related->title }}" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h6 class="card-title">{{ $related->title }}</h6>
                            <p class="card-text text-muted small">{{ Str::limit($related->excerpt, 80) }}</p>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="{{ route('blog.show', $related) }}" class="btn btn-sm btn-outline-primary">Read</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
