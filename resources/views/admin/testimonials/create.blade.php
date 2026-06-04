@extends('layouts.admin')
@section('page-title', 'Add Testimonial')
@section('content')
<div class="card">
    <div class="card-header">New Testimonial</div>
    <div class="card-body">
        <form action="{{ route('admin.testimonials.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Guest Name</label>
                <input type="text" name="guest_name" class="form-control @error('guest_name') is-invalid @enderror" value="{{ old('guest_name') }}" required>
                @error('guest_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Guest Title</label>
                        <input type="text" name="guest_title" class="form-control @error('guest_title') is-invalid @enderror" value="{{ old('guest_title') }}" placeholder="e.g., CEO, Tourist">
                        @error('guest_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Guest Country</label>
                        <input type="text" name="guest_country" class="form-control @error('guest_country') is-invalid @enderror" value="{{ old('guest_country') }}">
                        @error('guest_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Testimonial Content</label>
                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="4" required>{{ old('content') }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                    <option value="">Select Rating</option>
                    @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ old('rating')==$i?'selected':'' }}>
                        {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                    </option>
                    @endfor
                </select>
                @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <x-image-picker name="guest_avatar_id" label="Guest Avatar" type="avatar" folder="bellevie_hotel/testimonials" />
                @error('guest_avatar_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_featured" class="form-check-input" id="featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label" for="featured">Featured Testimonial</label>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>
                </div>
            </div>

            <button class="btn btn-primary" style="background:#C9A227;border:none;">Create Testimonial</button>
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
