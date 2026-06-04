@extends('layouts.admin')
@section('page-title', 'Room Management')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Room
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search...">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach($roomTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm datatable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Room #</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Bed</th>
                        <th>Capacity</th>
                        <th>Price</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                    <tr>
                        <td>
                            @if($room->featured_image)
                            <img src="{{ $room->featured_image }}" alt="{{ $room->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            @else
                            <span style="color: #ccc;">No image</span>
                            @endif
                        </td>
                        <td>{{ $room->name }}</td>
                        <td>{{ $room->room_number }}</td>
                        <td>{{ $room->roomType->name }}</td>
                        <td>{{ $room->floor ?? '-' }}</td>
                        <td>{{ $room->bed_type }}</td>
                        <td>{{ $room->max_adults }} pax</td>
                        <td>${{ $room->price_per_night }}</td>
                        <td>
                            <form action="{{ route('admin.rooms.toggle', $room) }}" method="POST" style="display: inline;">
                                @csrf
                                <div class="form-check form-switch" onclick="this.querySelector('button').click()">
                                    <input class="form-check-input" type="checkbox" {{ $room->is_active ? 'checked' : '' }}>
                                </div>
                                <button type="submit" style="display: none;"></button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $rooms->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
