@extends('layouts.admin')
@section('page-title', 'Edit Category')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>
<div class="card" style="max-width:600px;">
    <div class="card-header">Edit: {{ $blogCategory->name }}</div>
    <div class="card-body">
        <form action="{{ route('admin.blog-categories.update', $blogCategory) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $blogCategory->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" class="form-control" value="{{ $blogCategory->slug }}" disabled>
                <small class="text-muted">Auto-updated from name</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $blogCategory->description) }}</textarea>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $blogCategory->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <button class="btn text-white" style="background:#C9A227;border:none;">Update Category</button>
            <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
