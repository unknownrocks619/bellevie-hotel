@php
    $editing = isset($category);
    $old = fn($key, $default = null) => old($key, $editing ? $category->{$key} : $default);
@endphp

<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $old('name') }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Slug <small class="text-muted">(leave blank to auto-generate)</small></label>
    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ $old('slug') }}">
    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="2">{{ $old('description') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Sort Order</label>
    <input type="number" name="sort_order" min="0" class="form-control" style="max-width:160px;" value="{{ $old('sort_order', 0) }}">
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="is_active" class="form-check-input" id="active" value="1" {{ $old('is_active', true) ? 'checked' : '' }}>
    <label class="form-check-label" for="active">Active</label>
</div>
