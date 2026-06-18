@extends('layouts.app')
@section('content')

@include('frontend.partials.page-hero', [
    'eyebrow'     => 'INSIGHTS & STORIES',
    'title'       => isset($category) ? $category->name : 'Hotel Blog',
    'subtitle'    => 'Stories, tips, and inspiration from Bellevie Hotel',
    'breadcrumbs' => array_filter([
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Blog', 'url' => route('blog.index')],
        isset($category) ? ['label' => $category->name] : null,
    ]),
])

<div class="container py-5">
    @if(isset($category))
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>
    <div class="mb-4 p-4 rounded" style="background:#fdf8ea;border-left:4px solid #C9A227;">
        <h4 class="mb-1" style="color:#C9A227;">{{ $category->name }}</h4>
        @if($category->description)<p class="text-muted mb-0">{{ $category->description }}</p>@endif
    </div>
    @endif
    <div class="mb-4">
        <h5>Filter by Category</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary btn-sm {{ !request('category')?'active':''; }}" style="{{ !request('category')?'background:#C9A227;color:white;border:none;':'' }}">All Posts</a>
            @if(isset($categories))
            @foreach($categories as $category)
            <a href="{{ route('blog.index', ['category' => $category->slug]) }}" class="btn btn-outline-secondary btn-sm {{ request('category')==$category->slug?'active':''; }}" style="{{ request('category')==$category->slug?'background:#C9A227;color:white;border:none;':'' }}">{{ $category->name }}</a>
            @endforeach
            @endif
        </div>
    </div>

    <div class="row g-4">
        @forelse($posts as $post)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow-lg" style="transition: box-shadow 0.3s;">
                @php $postFeaturedUrl = $post->featuredImageUrl($post->featured_image ?? ''); @endphp
                @if($postFeaturedUrl)
                <img src="{{ $postFeaturedUrl }}" class="card-img-top" alt="{{ $post->title }}" style="height: 250px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <div class="mb-2">
                        @if($post->category)
                        <span class="badge" style="background:#C9A227;">{{ $post->category->name }}</span>
                        @endif
                        @if($post->is_featured)
                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Featured</span>
                        @endif
                    </div>
                    <h6 class="card-title">{{ $post->title }}</h6>
                    <p class="card-text text-muted">{{ Str::limit($post->excerpt, 100) }}</p>
                    <small class="text-muted d-block mb-3">
                        <i class="bi bi-calendar"></i> {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('M d, Y') : 'Not published' }}
                    </small>
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('blog.show', $post) }}" class="btn btn-sm btn-outline-primary">Read More</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No blog posts available at the moment.
            </div>
        </div>
        @endforelse
    </div>

    @if($posts->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $posts->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
