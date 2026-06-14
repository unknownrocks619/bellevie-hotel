@extends('layouts.app')
@section('title', ($post->meta_title ?: $post->title) . ' | ' . \App\Models\Setting::get('hotel_name', 'Bellevie Hotel'))
@section('seo_title', $seoTitle ?? $post->title)
@section('seo_description', $seoDescription ?? $post->excerpt)
@if(!empty($seoImage)) @section('seo_image', $seoImage) @endif
@section('content')
@include('frontend.partials.page-hero', [
    'eyebrow'     => ($post->category->name ?? null) ? strtoupper($post->category->name) : 'HOTEL BLOG',
    'title'       => $post->title,
    'subtitle'    => $post->published_at
        ? 'Published on ' . \Carbon\Carbon::parse($post->published_at)->format('F d, Y')
        : null,
    'breadcrumbs' => array_filter([
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Blog', 'url' => route('blog.index')],
        $post->category ? ['label' => $post->category->name, 'url' => route('blog.index', ['category' => $post->category->slug])] : null,
        ['label' => \Illuminate\Support\Str::limit($post->title, 40)],
    ]),
])

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
