@extends('layouts.admin')
@section('page-title', 'Guests')
@section('content')
<div class="row mb-3">
    <div class="col-md-6">
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> Guests are automatically created when bookings are made.
        </div>
    </div>
    <div class="col-md-6">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or email..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-search"></i></button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm datatable">
                <thead><tr>
                    <th>Name</th><th>Email</th><th>Phone</th>
                    <th>VIP Status</th><th>Total Spent</th><th>Bookings</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($guests as $guest)
                <tr>
                    <td>
                        {{ $guest->first_name }} {{ $guest->last_name }}
                        @if($guest->is_blacklisted)
                        <span class="badge bg-danger ms-1">Blacklisted</span>
                        @endif
                    </td>
                    <td>{{ $guest->email }}</td>
                    <td>{{ $guest->phone ?? '-' }}</td>
                    <td>
                        @php $vip_colors=['regular'=>'secondary','silver'=>'info','gold'=>'warning','platinum'=>'dark']; @endphp
                        <span class="badge bg-{{ $vip_colors[$guest->vip_status]??'secondary' }}">{{ ucfirst($guest->vip_status) }}</span>
                    </td>
                    <td>${{ number_format($guest->total_spent ?? 0, 2) }}</td>
                    <td><span class="badge bg-light text-dark">{{ $guest->bookings_count ?? 0 }}</span></td>
                    <td>
                        <a href="{{ route('admin.guests.show',$guest) }}" class="btn btn-sm btn-outline-primary">View</a>
                        <a href="{{ route('admin.guests.edit',$guest) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No guests found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($guests->hasPages())
        {{ $guests->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
@endsection
