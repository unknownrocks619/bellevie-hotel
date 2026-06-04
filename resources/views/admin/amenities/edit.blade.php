@extends('layouts.admin')
@section('page-title', 'Edit Amenity')
@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('admin.amenities.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Amenities
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">Edit: {{ $amenity->name }}</div>
    <div class="card-body" style="max-width:600px;">
        <form action="{{ route('admin.amenities.update', $amenity) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $amenity->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Bootstrap Icon</label>
                <div class="input-group">
                    <span class="input-group-text fs-5"><i id="iconPreview" class="bi {{ $amenity->icon ?? 'bi-star' }}"></i></span>
                    <input type="text" name="icon" id="iconInput" class="form-control"
                           value="{{ old('icon', $amenity->icon) }}" placeholder="bi-wifi"
                           oninput="document.getElementById('iconPreview').className='bi '+this.value">
                </div>
                <small class="text-muted">Full Bootstrap Icon class, e.g. <code>bi-wifi</code>, <code>bi-tv</code></small>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                           value="1" {{ old('is_active', $amenity->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn" style="background:#C9A227;color:white;border:none;">Update Amenity</button>
                <a href="{{ route('admin.amenities.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
