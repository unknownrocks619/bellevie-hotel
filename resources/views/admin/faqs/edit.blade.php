@extends('layouts.admin')
@section('page-title', 'Edit FAQ')
@section('content')

<div class="mb-3">
    <a href="{{ route('admin.faqs.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to FAQs
    </a>
</div>

<div class="card" style="max-width:760px;">
    <div class="card-header">
        Edit FAQ
        <span class="badge ms-2 rounded-pill" style="background:#C9A22720;color:#C9A227;border:1px solid #C9A22740;font-size:0.72rem;">
            {{ $faq->category_label }}
        </span>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Question / Title <span class="text-danger">*</span></label>
                <input type="text" name="title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $faq->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Answer / Description <span class="text-danger">*</span></label>
                <textarea name="description" rows="6"
                          class="form-control @error('description') is-invalid @enderror"
                          required>{{ old('description', $faq->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Basic HTML is supported (bold, links, lists).</small>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                        @foreach($categories as $slug => $label)
                        <option value="{{ $slug }}" {{ old('category', $faq->category) === $slug ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="{{ old('sort_order', $faq->sort_order) }}" min="0">
                </div>
                <div class="col-md-3 d-flex align-items-end pb-1">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="is_active" value="1" {{ old('is_active', $faq->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn text-white" style="background:#C9A227;border:none;">
                    <i class="bi bi-floppy me-1"></i>Save Changes
                </button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
