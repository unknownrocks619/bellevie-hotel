@extends('layouts.admin')
@section('page-title', 'Add Room Type')
@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('admin.room-types.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Room Types
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">New Room Type</div>
    <div class="card-body">
        <form action="{{ route('admin.room-types.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required placeholder="e.g. Deluxe Suite">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Icon Class</label>
                    <div class="input-group">
                        <span class="input-group-text"><i id="iconPreview" class="bi bi-door-closed"></i></span>
                        <input type="text" name="icon" id="iconInput" class="form-control"
                               value="{{ old('icon', 'bi-door-closed') }}"
                               placeholder="bi-door-closed" oninput="updateIcon(this.value)">
                    </div>
                    <small class="text-muted">Bootstrap Icon class, e.g. <code>bi-star</code></small>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe this room type…">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Max Adults <span class="text-danger">*</span></label>
                    <input type="number" name="max_adults" class="form-control @error('max_adults') is-invalid @enderror"
                           value="{{ old('max_adults', 2) }}" min="1" required>
                    @error('max_adults')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Max Children</label>
                    <input type="number" name="max_children" class="form-control @error('max_children') is-invalid @enderror"
                           value="{{ old('max_children', 0) }}" min="0" required>
                    @error('max_children')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn" style="background:#C9A227;color:white;border:none;">Create Room Type</button>
                <a href="{{ route('admin.room-types.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function updateIcon(val) {
    document.getElementById('iconPreview').className = 'bi ' + val;
}
</script>
@endsection
