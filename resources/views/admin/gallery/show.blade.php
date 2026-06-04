@extends('layouts.admin')
@section('page-title', $image->title ?? 'Gallery Image')
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $image->title ?? 'Untitled' }}</span>
                <div>
                    <a href="{{ route('admin.gallery.edit',$image) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.gallery.destroy',$image) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this image?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($image->image_url)
                <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                @endif

                <table class="table table-sm">
                    <tr><td><strong>Title:</strong></td><td>{{ $image->title ?? '-' }}</td></tr>
                    <tr><td><strong>Category:</strong></td><td>{{ ucfirst($image->category ?? '-') }}</td></tr>
                    <tr><td><strong>Alt Text:</strong></td><td>{{ $image->alt_text ?? '-' }}</td></tr>
                    <tr><td><strong>Sort Order:</strong></td><td>{{ $image->sort_order ?? '0' }}</td></tr>
                    <tr><td><strong>Created:</strong></td><td>{{ $image->created_at->format('M d, Y H:i') }}</td></tr>
                    <tr><td><strong>Updated:</strong></td><td>{{ $image->updated_at->format('M d, Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary mt-3">← Back to Gallery</a>
@endsection
