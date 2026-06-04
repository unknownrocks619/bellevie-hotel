@extends('layouts.admin')
@section('page-title', $roomType->name)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $roomType->name }}</span>
                <div>
                    <a href="{{ route('admin.room-types.edit',$roomType) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.room-types.destroy',$roomType) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this room type?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td><strong>Name:</strong></td><td>{{ $roomType->name }}</td></tr>
                    <tr><td><strong>Slug:</strong></td><td><code>{{ $roomType->slug }}</code></td></tr>
                    <tr><td><strong>Max Adults:</strong></td><td>{{ $roomType->max_adults }}</td></tr>
                    <tr><td><strong>Max Children:</strong></td><td>{{ $roomType->max_children }}</td></tr>
                    <tr><td><strong>Icon:</strong></td><td><i class="bi {{ $roomType->icon }}"></i> {{ $roomType->icon }}</td></tr>
                    <tr><td><strong>Active:</strong></td><td>{{ $roomType->is_active ? 'Yes' : 'No' }}</td></tr>
                </table>
                @if($roomType->description)
                <hr>
                <h5>Description</h5>
                <p>{{ $roomType->description }}</p>
                @endif
            </div>
        </div>

        @if($roomType->rooms && $roomType->rooms->count() > 0)
        <div class="card">
            <div class="card-header">Rooms of this Type</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr>
                            <th>Room Number</th><th>Name</th><th>Price/Night</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                        @foreach($roomType->rooms as $room)
                        <tr>
                            <td>{{ $room->room_number }}</td>
                            <td><a href="{{ route('admin.rooms.show',$room) }}">{{ $room->name }}</a></td>
                            <td>${{ number_format($room->price_per_night, 2) }}</td>
                            <td>
                                @php $statusColors=['available'=>'success','occupied'=>'danger','maintenance'=>'warning']; @endphp
                                <span class="badge bg-{{ $statusColors[$room->status]??'secondary' }}">{{ ucfirst($room->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
<a href="{{ route('admin.room-types.index') }}" class="btn btn-secondary mt-3">← Back to Room Types</a>
@endsection
