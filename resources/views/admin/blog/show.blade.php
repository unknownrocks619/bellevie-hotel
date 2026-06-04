@extends('layouts.admin')
@section('page-title', $post->title)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $post->title }}</span>
                <div>
                    <a href="{{ route('admin.blog.edit',$post) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.blog.destroy',$post) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this post?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($post->featured_image)
                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                @endif

                <p><strong>Status:</strong> <span class="badge bg-{{ $post->status=='published'?'success':'secondary' }}">{{ ucfirst($post->status) }}</span></p>
                <p><strong>Category:</strong> {{ $post->category->name ?? '-' }}</p>
                <p><strong>Featured:</strong> {{ $post->is_featured ? 'Yes' : 'No' }}</p>
                <p><strong>Published At:</strong> {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('M d, Y H:i') : '-' }}</p>

                <hr>

                <h5>Excerpt</h5>
                <p>{{ $post->excerpt ?? '-' }}</p>

                <h5>Content</h5>
                <div class="border-top pt-3">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Post Info</div>
            <div class="card-body">
                <p><strong>Slug:</strong><br><code>{{ $post->slug }}</code></p>
                <p><strong>Created:</strong><br>{{ $post->created_at->format('M d, Y H:i') }}</p>
                <p><strong>Updated:</strong><br>{{ $post->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.blog.index') }}" class="btn btn-secondary mt-3">← Back to Blog</a>
@endsection
