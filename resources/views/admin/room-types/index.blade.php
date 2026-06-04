@extends('layouts.admin')
@section('page-title', 'Room Types')
@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <a href="{{ route('admin.room-types.create') }}" class="btn btn-primary" style="background:#C9A227;border:none;"><i class="bi bi-plus-circle"></i> Add Room Type</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm datatable">
                <thead><tr>
                    <th>Name</th><th>Slug</th><th>Max Adults</th><th>Max Children</th><th>Rooms Count</th><th>Active</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($roomTypes as $type)
                <tr>
                    <td>{{ $type->name }}</td>
                    <td><code>{{ $type->slug }}</code></td>
                    <td>{{ $type->max_adults }}</td>
                    <td>{{ $type->max_children }}</td>
                    <td><span class="badge bg-light text-dark">{{ $type->rooms_count ?? 0 }}</span></td>
                    <td>
                        @if($type->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.room-types.edit',$type) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.room-types.destroy',$type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this room type?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No room types found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($roomTypes->hasPages())
        {{ $roomTypes->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
@endsection
