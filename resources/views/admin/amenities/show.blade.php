@extends('layouts.admin')
@section('page-title', $amenity->name)
@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $amenity->name }}</span>
                <div>
                    <a href="{{ route('admin.amenities.edit',$amenity) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.amenities.destroy',$amenity) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this amenity?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <i class="bi {{ $amenity->icon }}" style="font-size: 48px; color:#C9A227;"></i>
                </div>
                <table class="table table-sm">
                    <tr><td><strong>Name:</strong></td><td>{{ $amenity->name }}</td></tr>
                    <tr><td><strong>Icon:</strong></td><td><code>{{ $amenity->icon }}</code></td></tr>
                    <tr><td><strong>Active:</strong></td><td>{{ $amenity->is_active ? 'Yes' : 'No' }}</td></tr>
                    <tr><td><strong>Created:</strong></td><td>{{ $amenity->created_at->format('M d, Y H:i') }}</td></tr>
                    <tr><td><strong>Updated:</strong></td><td>{{ $amenity->updated_at->format('M d, Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.amenities.index') }}" class="btn btn-secondary mt-3">← Back to Amenities</a>
@endsection
