@php
    $editing = isset($menuItem);
    $old = fn($key, $default = null) => old($key, $editing ? $menuItem->{$key} : $default);
    $defaultCategoryId = $editing ? $menuItem->category_id : ($selectedCategoryId ?? null);
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                <option value="">Select…</option>
                @foreach($categories as $id => $name)
                <option value="{{ $id }}" {{ (string) $old('category_id', $defaultCategoryId) === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Slug <small class="text-muted">(leave blank to auto-generate)</small></label>
    <input type="text" name="slug" class="form-control" value="{{ $old('slug') }}">
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="3">{{ $old('description') }}</textarea>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Price <small class="text-muted">(blank = market price)</small></label>
            <input type="number" name="price" min="0" step="0.01" class="form-control" value="{{ $old('price') }}">
            <div class="form-check mt-2">
                <input type="checkbox" name="show_price" class="form-check-input" id="show_price" value="1" {{ $old('show_price', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="show_price">Show price on menu <small class="text-muted">(uncheck to hide it from visitors)</small></label>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Dietary Tags <small class="text-muted">(comma separated)</small></label>
            <input type="text" name="dietary_tags" class="form-control" value="{{ $old('dietary_tags') }}" placeholder="Vegan, Gluten-Free">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" min="0" class="form-control" value="{{ $old('sort_order', 0) }}">
        </div>
    </div>
</div>

<div class="mb-3">
    <x-image-picker name="image_id" label="Dish Photo" type="featured" folder="bellevie_hotel/restaurant"
        :value="$featuredImage ?? null" />
</div>

<div class="mb-3">
    <div class="form-check">
        <input type="checkbox" name="is_featured" class="form-check-input" id="featured" value="1" {{ $old('is_featured') ? 'checked' : '' }}>
        <label class="form-check-label" for="featured">Featured <small class="text-muted">(shown in Chef's Featured Selection)</small></label>
    </div>
    <div class="form-check">
        <input type="checkbox" name="is_active" class="form-check-input" id="active" value="1" {{ $old('is_active', true) ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Active</label>
    </div>
</div>
