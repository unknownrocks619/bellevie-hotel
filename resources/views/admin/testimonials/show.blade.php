@extends('layouts.admin')
@section('page-title', $testimonial->guest_name)
@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $testimonial->guest_name }}</span>
                <div>
                    <a href="{{ route('admin.testimonials.edit',$testimonial) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.testimonials.destroy',$testimonial) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this testimonial?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    @for($i = 1; $i <= $testimonial->rating; $i++)
                    <i class="bi bi-star-fill" style="color:#C9A227; font-size: 24px;"></i>
                    @endfor
                </div>

                <blockquote class="blockquote mb-4">
                    <p class="mb-0">{{ $testimonial->content }}</p>
                </blockquote>

                <hr>

                <p><strong>Guest Title:</strong> {{ $testimonial->guest_title ?? '-' }}</p>
                <p><strong>Country:</strong> {{ $testimonial->guest_country ?? '-' }}</p>
                <p><strong>Rating:</strong> {{ $testimonial->rating }}/5</p>
                <p><strong>Featured:</strong> {{ $testimonial->is_featured ? 'Yes' : 'No' }}</p>
                <p><strong>Active:</strong> {{ $testimonial->is_active ? 'Yes' : 'No' }}</p>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary mt-3">← Back to Testimonials</a>
@endsection
