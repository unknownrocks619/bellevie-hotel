{{--
    Shared Event / Conference form fields.
    Expects: $types, optionally $event (edit mode), $selectedType (create mode), $featuredImage (edit mode).
--}}
@php
    $editing = isset($event);
    $old = fn($key, $default = null) => old($key, $editing ? $event->{$key} : $default);
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                   value="{{ $old('title') }}" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                @foreach($types as $slug => $label)
                <option value="{{ $slug }}" {{ $old('type', $selectedType ?? 'event') === $slug ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Slug <small class="text-muted">(leave blank to auto-generate from title)</small></label>
    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
           value="{{ $old('slug') }}" placeholder="e.g. summer-gala-2026">
    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Short Summary <small class="text-muted">(shown on cards & listings)</small></label>
    <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" rows="2"
              maxlength="500">{{ $old('excerpt') }}</textarea>
    @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <div id="quill-editor" style="height:300px;background:#fff;"></div>
    <input type="hidden" name="description" id="description" value="{{ $old('description') }}">
    @error('description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Starts At</label>
            <input type="datetime-local" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror"
                   value="{{ old('starts_at', $editing && $event->starts_at ? $event->starts_at->format('Y-m-d\TH:i') : '') }}">
            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Ends At <small class="text-muted">(optional)</small></label>
            <input type="datetime-local" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror"
                   value="{{ old('ends_at', $editing && $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '') }}">
            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Venue</label>
            <input type="text" name="venue" class="form-control @error('venue') is-invalid @enderror"
                   value="{{ $old('venue') }}" placeholder="e.g. Grand Ballroom, Bellevie Hotel">
            @error('venue')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Organizer</label>
            <input type="text" name="organizer" class="form-control @error('organizer') is-invalid @enderror"
                   value="{{ $old('organizer') }}">
            @error('organizer')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Capacity <small class="text-muted">(optional)</small></label>
            <input type="number" name="capacity" min="1" class="form-control @error('capacity') is-invalid @enderror"
                   value="{{ $old('capacity') }}">
            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Price <small class="text-muted">(blank = free / on request)</small></label>
            <input type="number" name="price" min="0" step="0.01" class="form-control @error('price') is-invalid @enderror"
                   value="{{ $old('price') }}">
            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ $old('sort_order', 0) }}">
            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Button Text <small class="text-muted">(e.g. Register Now)</small></label>
            <input type="text" name="cta_text" class="form-control @error('cta_text') is-invalid @enderror"
                   value="{{ $old('cta_text') }}">
            @error('cta_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Button URL</label>
            <input type="text" name="cta_url" class="form-control @error('cta_url') is-invalid @enderror"
                   value="{{ $old('cta_url') }}" placeholder="/contact or https://…">
            @error('cta_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <x-image-picker name="image_id" label="Featured Image" type="featured" folder="bellevie_hotel/events"
        :value="$featuredImage ?? null" />
    @error('image_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <div class="form-check">
        <input type="checkbox" name="is_featured" class="form-check-input" id="featured" value="1"
               {{ $old('is_featured') ? 'checked' : '' }}>
        <label class="form-check-label" for="featured">Featured</label>
    </div>
    <div class="form-check">
        <input type="checkbox" name="is_active" class="form-check-input" id="active" value="1"
               {{ old('is_active', $editing ? $event->is_active : true) ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Active</label>
    </div>
</div>

@push('page_script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
const quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ header: [2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link', 'clean'],
        ],
    },
});
const existing = document.getElementById('description').value;
if (existing) quill.root.innerHTML = existing;
document.querySelector('#quill-editor').closest('form').addEventListener('submit', () => {
    document.getElementById('description').value = quill.root.innerHTML;
});
</script>
@endpush
