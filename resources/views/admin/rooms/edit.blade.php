@extends('layouts.admin')
@section('page-title', 'Edit Room')

@section('content')
    <form action="{{ route('admin.rooms.update', $room) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">

            {{-- LEFT: tabbed content --}}
            <div class="col-lg-8">

                <ul class="nav nav-tabs mb-0" style="border-bottom:none;">
                    <li class="nav-item">
                        <button class="nav-link active px-4 text-dark" type="button" data-bs-toggle="tab"
                            data-bs-target="#tabDetails">
                            <i class="bi bi-door-open me-2"></i>Room Details
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link {{ $seo ? 'text-success' : '' }} px-4 text-dark" type="button"
                            data-bs-toggle="tab" data-bs-target="#tabSeo">
                            <i class="bi bi-search me-2"></i>SEO
                            @if ($seo)
                                <i class="bi bi-check-circle-fill ms-1" style="font-size:0.75rem;"></i>
                            @endif
                        </button>
                    </li>
                </ul>

                <div class="tab-content" style="border:1px solid #dee2e6; border-top:none; border-radius:0 0 8px 8px;">

                    {{-- Details Tab --}}
                    <div class="tab-pane fade show active p-0" id="tabDetails">

                        <div class="card border-0 border-bottom mb-0" style="border-radius:0;">
                            <div class="card-header" style="border-radius:0;">Room Information</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Room Name</label>
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $room->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Room Number</label>
                                            <input type="text" name="room_number"
                                                class="form-control @error('room_number') is-invalid @enderror"
                                                value="{{ old('room_number', $room->room_number) }}" required>
                                            @error('room_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Room Type</label>
                                            <select name="room_type_id"
                                                class="form-select @error('room_type_id') is-invalid @enderror" required>
                                                <option value="">Select Room Type</option>
                                                @foreach ($roomTypes as $roomType)
                                                    <option value="{{ $roomType->id }}"
                                                        {{ old('room_type_id', $room->room_type_id) == $roomType->id ? 'selected' : '' }}>
                                                        {{ $roomType->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('room_type_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Bed Type</label>
                                            <select name="bed_type"
                                                class="form-select @error('bed_type') is-invalid @enderror">
                                                <option value="">Select Bed Type</option>
                                                @foreach (['King', 'Queen', 'Twin', 'Double', 'Single'] as $bt)
                                                    <option value="{{ $bt }}"
                                                        {{ old('bed_type', $room->bed_type) == $bt ? 'selected' : '' }}>
                                                        {{ $bt }}</option>
                                                @endforeach
                                            </select>
                                            @error('bed_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Price per Night</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" name="price_per_night"
                                                    class="form-control @error('price_per_night') is-invalid @enderror"
                                                    value="{{ old('price_per_night', $room->price_per_night) }}" required>
                                                @error('price_per_night')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Weekend Price (Optional)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" name="weekend_price"
                                                    class="form-control @error('weekend_price') is-invalid @enderror"
                                                    value="{{ old('weekend_price', $room->weekend_price) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Size (sq ft)</label>
                                            <input type="number" name="size_sqft" class="form-control"
                                                value="{{ old('size_sqft', $room->size_sqft) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Floor</label>
                                            <input type="number" name="floor" class="form-control"
                                                value="{{ old('floor', $room->floor) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">View Type</label>
                                            <input type="text" name="view_type" class="form-control"
                                                value="{{ old('view_type', $room->view_type) }}"
                                                placeholder="Ocean, Garden…">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-0">
                                            <label class="form-label">Max Adults</label>
                                            <input type="number" name="max_adults"
                                                class="form-control @error('max_adults') is-invalid @enderror"
                                                value="{{ old('max_adults', $room->max_adults ?? 2) }}" min="1">
                                            @error('max_adults')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-0">
                                            <label class="form-label">Max Children</label>
                                            <input type="number" name="max_children"
                                                class="form-control @error('max_children') is-invalid @enderror"
                                                value="{{ old('max_children', $room->max_children ?? 0) }}"
                                                min="0">
                                            @error('max_children')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 border-bottom mb-0" style="border-radius:0;">
                            <div class="card-header" style="border-radius:0;">Description</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Short Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $room->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Full Content (Rich Text)</label>
                                    <div id="quill-editor" style="height:300px;"></div>
                                    <textarea name="content" id="content" class="d-none">{{ old('content', $room->content) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 mb-0" style="border-radius:0 0 8px 8px;">
                            <div class="card-header" style="border-radius:0;">Amenities</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach ($amenities as $amenity)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="amenities[]"
                                                    value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}"
                                                    {{ $room->amenities->contains($amenity->id) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="amenity_{{ $amenity->id }}">
                                                    @if ($amenity->icon)
                                                        <i class="{{ $amenity->icon }}"></i>
                                                    @endif
                                                    {{ $amenity->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- SEO Tab --}}
                    <div class="tab-pane fade p-4 bg-white" id="tabSeo">
                        @include('admin.partials.seo-tab', ['seo' => $seo])
                    </div>

                </div>
            </div>

            {{-- RIGHT: always-visible sidebar --}}
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Publish</div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                value="1" {{ old('is_active', $room->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                value="1" {{ old('is_featured', $room->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Featured Room</label>
                        </div>

                        {{-- Price Display Toggle --}}
                        <div class="rounded p-3 mb-4" style="background:#fdf8ea;border:1px solid #f0e0b0;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div>
                                    <span class="fw-semibold" style="font-size:.85rem;color:#5a4200;">
                                        <i class="bi bi-tag-fill me-1" style="color:#C9A227;"></i>Show Price
                                    </span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="show_price" id="show_price"
                                        value="1"
                                        {{ old('show_price', $room->show_price ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_price"></label>
                                </div>
                            </div>
                            <p class="mb-0" style="font-size:.74rem;color:#8a6d00;line-height:1.4;">
                                When off, the price is hidden on the listings &amp; detail page. Guests see
                                <em>"Price on request"</em> instead.
                            </p>
                        </div>

                        <button type="submit" class="btn w-100 text-white"
                            style="background:#C9A227;border:none;">Update Room</button>
                        <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary w-100 mt-2">Cancel</a>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Featured Image</div>
                    <div class="card-body">
                        <x-image-picker name="featured_image_id" label="" type="featured" :value="$featuredImage ?? null"
                            folder="bellevie_hotel/rooms" placeholder="No image selected" />
                        @error('featured_image_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Gallery Images</div>
                    <div class="card-body">
                        <x-image-picker name="gallery_image_ids" label="" :multiple="true" type="gallery"
                            :value="$galleryImages ?? collect()" folder="bellevie_hotel/rooms/gallery" placeholder="No images" />
                        @error('gallery_image_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">Upload or select multiple images from the library.</small>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection


@push('page_script')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        const quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        header: [1, 2, 3, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        list: 'ordered'
                    }, {
                        list: 'bullet'
                    }],
                    ['link', 'blockquote', 'code-block'],
                    ['clean']
                ]
            },
            placeholder: 'Write your content here…'
        });

        $('form').on('submit', function(e) {
            $('#content').val(quill.root.innerHTML);
        })

        const existing = document.getElementById('content').value;
        if (existing) quill.root.innerHTML = existing;
    </script>
@endpush
