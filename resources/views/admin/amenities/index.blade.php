@extends('layouts.admin')
@section('page-title', 'Amenities')
@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <a href="{{ route('admin.amenities.create') }}" class="btn btn-primary" style="background:#C9A227;border:none;"><i class="bi bi-plus-circle"></i> Add Amenity</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm datatable">
                <thead><tr>
                    <th>Icon</th><th>Name</th><th>Active</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($amenities as $amenity)
                <tr>
                    <td><i class="bi {{ $amenity->icon }}" style="font-size: 18px;"></i></td>
                    <td>{{ $amenity->name }}</td>
                    <td>
                        @if($amenity->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.amenities.edit',$amenity) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.amenities.destroy',$amenity) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this amenity?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No amenities found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($amenities->hasPages())
        {{ $amenities->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
@endsection
