@extends('layouts.admin')
@section('page-title', 'Upload Images')
@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Gallery
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">Upload Gallery Images</div>
    <div class="card-body">
        <form action="{{ route('admin.gallery.store') }}" method="POST" id="uploadForm">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" name="category" class="form-control @error('category') is-invalid @enderror"
                               list="categoryList" placeholder="e.g. Rooms, Dining, Pool…"
                               value="{{ old('category') }}" required>
                        <datalist id="categoryList">
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                            <option value="Rooms">
                            <option value="Lobby">
                            <option value="Dining">
                            <option value="Pool & Spa">
                            <option value="Events">
                            <option value="Exterior">
                        </datalist>
                    </div>
                    @error('category')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Default Title (optional)</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                           placeholder="Leave blank to use filename">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Alt Text (optional)</label>
                    <input type="text" name="alt_text" class="form-control" value="{{ old('alt_text') }}"
                           placeholder="Describe the image for accessibility">
                </div>
            </div>

            <!-- Image Picker -->
            <div class="mb-4">
                <x-image-picker name="image_ids" label="Gallery Images" :multiple="true" type="gallery" folder="bellevie_hotel/gallery" />
                @error('image_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn" style="background:#C9A227;color:white;border:none;" id="submitBtn">
                    <i class="bi bi-upload me-1"></i>Upload Images
                </button>
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
