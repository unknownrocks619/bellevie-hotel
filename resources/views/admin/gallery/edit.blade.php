@extends('layouts.admin')
@section('page-title', 'Edit Gallery Image')
@section('content')
<div class="card">
    <div class="card-header">Edit Gallery Image</div>
    <div class="card-body">
        <form action="{{ route('admin.gallery.update', $gallery) }}" method="POST">
            @csrf @method('PUT')

            @if($gallery->image_url)
            <div class="mb-3">
                <label class="form-label">Current Image</label>
                <div>
                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" style="max-width: 300px; max-height: 300px;">
                </div>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $gallery->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror"
                       list="galEditCategoryList" value="{{ old('category', $gallery->category) }}" required>
                <datalist id="galEditCategoryList">
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}">
                    @endforeach
                    <option value="Rooms"><option value="Lobby">
                    <option value="Dining"><option value="Pool & Spa">
                    <option value="Events"><option value="Exterior">
                </datalist>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $gallery->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (visible on website)</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alt Text</label>
                <input type="text" name="alt_text" class="form-control @error('alt_text') is-invalid @enderror" value="{{ old('alt_text', $gallery->alt_text) }}" required>
                @error('alt_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $gallery->sort_order ?? 0) }}">
                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button class="btn btn-primary" style="background:#C9A227;border:none;">Update Image</button>
            <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
