@extends('layouts.admin')
@section('page-title', 'Testimonials')
@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary" style="background:#C9A227;border:none;"><i class="bi bi-plus-circle"></i> Add Testimonial</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm datatable">
                <thead><tr>
                    <th>Guest Name</th><th>Title</th><th>Country</th><th>Rating</th><th>Featured</th><th>Active</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($testimonials as $testimonial)
                <tr>
                    <td>{{ $testimonial->guest_name }}</td>
                    <td>{{ $testimonial->guest_title ?? '-' }}</td>
                    <td>{{ $testimonial->guest_country ?? '-' }}</td>
                    <td>
                        @for($i = 1; $i <= $testimonial->rating; $i++)
                        <i class="bi bi-star-fill" style="color:#C9A227;"></i>
                        @endfor
                    </td>
                    <td>
                        @if($testimonial->is_featured)
                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i></span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($testimonial->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.testimonials.edit',$testimonial) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.testimonials.destroy',$testimonial) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this testimonial?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No testimonials found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($testimonials->hasPages())
        {{ $testimonials->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
@endsection
