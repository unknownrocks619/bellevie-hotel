@extends('layouts.admin')
@section('page-title', 'Conference Hall Page')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <a href="{{ route('admin.conference-inquiries.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-inbox me-1"></i>View Inquiries
    </a>
    <a href="{{ route('conference.index') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-box-arrow-up-right me-1"></i>View Page
    </a>
</div>

<div class="card">
    <div class="card-header">Conference Page Content</div>
    <div class="card-body">
        <form action="{{ route('admin.conference.update') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Hero Title</label>
                        <input type="text" name="hero_title" class="form-control @error('hero_title') is-invalid @enderror"
                               value="{{ old('hero_title', $page->hero_title) }}" required>
                        @error('hero_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Hero Subtitle</label>
                        <input type="text" name="hero_subtitle" class="form-control"
                               value="{{ old('hero_subtitle', $page->hero_subtitle) }}">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <div id="quill-editor" style="height:260px;background:#fff;"></div>
                <input type="hidden" name="description" id="description" value="{{ old('description', $page->description) }}">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Capacity <small class="text-muted">(e.g. "Up to 250 guests")</small></label>
                        <input type="text" name="capacity_text" class="form-control" value="{{ old('capacity_text', $page->capacity_text) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Layout Options <small class="text-muted">(e.g. "Theatre, Boardroom, U-Shape")</small></label>
                        <input type="text" name="layout_text" class="form-control" value="{{ old('layout_text', $page->layout_text) }}">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <x-image-picker name="image_id" label="Hero Image" type="featured" folder="bellevie_hotel/conference"
                    :value="$featuredImage" />
            </div>

            <div class="mb-3">
                <x-image-picker name="gallery_image_ids" :multiple="true" label="Image Gallery" type="gallery"
                    folder="bellevie_hotel/conference" :value="$galleryImages" />
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $page->meta_description) }}">
                    </div>
                </div>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" class="form-check-input" id="active" value="1"
                       {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">
                    Active <small class="text-muted">(uncheck to hide the page from visitors — the page itself cannot be deleted)</small>
                </label>
            </div>

            <button class="btn text-white" style="background:#C9A227;border:none;">Save Changes</button>
        </form>
    </div>
</div>

@push('page_script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
const quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['link', 'clean']] },
});
const existing = document.getElementById('description').value;
if (existing) quill.root.innerHTML = existing;
document.querySelector('#quill-editor').closest('form').addEventListener('submit', () => {
    document.getElementById('description').value = quill.root.innerHTML;
});
</script>
@endpush
@endsection
