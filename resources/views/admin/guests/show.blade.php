@extends('layouts.admin')
@section('page-title', $guest->first_name . ' ' . $guest->last_name)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Guest Information</span>
                <a href="{{ route('admin.guests.edit',$guest) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $guest->first_name }} {{ $guest->last_name }}</p>
                        <p><strong>Email:</strong> {{ $guest->email }}</p>
                        <p><strong>Phone:</strong> {{ $guest->phone ?? '-' }}</p>
                        <p><strong>Nationality:</strong> {{ $guest->nationality ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        @php $vip_colors=['regular'=>'secondary','silver'=>'info','gold'=>'warning','platinum'=>'dark']; @endphp
                        <p><strong>VIP Status:</strong> <span class="badge bg-{{ $vip_colors[$guest->vip_status]??'secondary' }}">{{ ucfirst($guest->vip_status) }}</span></p>
                        <p><strong>Total Spent:</strong> ${{ number_format($guest->total_spent ?? 0, 2) }}</p>
                        <p><strong>Date of Birth:</strong> {{ $guest->date_of_birth ? \Carbon\Carbon::parse($guest->date_of_birth)->format('M d, Y') : '-' }}</p>
                        @if($guest->is_blacklisted)
                        <p><span class="badge bg-danger">BLACKLISTED</span></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Booking History</div>
            <div class="card-body">
                @if($guest->bookings && $guest->bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr>
                            <th>Reference</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                        @foreach($guest->bookings as $booking)
                        <tr>
                            <td><a href="{{ route('admin.bookings.show',$booking) }}">{{ $booking->booking_reference }}</a></td>
                            <td>{{ $booking->room->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</td>
                            <td>${{ number_format($booking->total_amount,2) }}</td>
                            <td>
                                @php $colors=['pending'=>'warning','confirmed'=>'success','checked_in'=>'info','checked_out'=>'secondary','cancelled'=>'danger','no_show'=>'dark']; @endphp
                                <span class="badge bg-{{ $colors[$booking->status]??'secondary' }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">No bookings yet</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">Add Note</div>
            <div class="card-body">
                <form action="{{ route('admin.guests.note', $guest) }}" method="POST">
                    @csrf
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Note Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="general">General</option>
                                <option value="booking">Booking</option>
                                <option value="complaint">Complaint</option>
                                <option value="compliment">Compliment</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Note</label>
                            <textarea name="note" class="form-control form-control-sm" rows="2"
                                      placeholder="Write a note about this guest…" required></textarea>
                        </div>
                    </div>
                    <button class="btn btn-sm" style="background:#C9A227;color:white;border:none;">
                        <i class="bi bi-plus-circle me-1"></i>Add Note
                    </button>
                </form>
            </div>
        </div>

        @if($guest->notes && $guest->notes->count() > 0)
        <div class="card mt-4">
            <div class="card-header">Notes ({{ $guest->notes->count() }})</div>
            <div class="card-body p-0">
                @php
                    $noteTypeColors = ['general'=>'secondary','booking'=>'info','complaint'=>'danger','compliment'=>'success'];
                @endphp
                @foreach($guest->notes->sortByDesc('created_at') as $note)
                <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <strong class="small">{{ $note->user->name ?? 'Staff' }}</strong>
                            <span class="badge bg-{{ $noteTypeColors[$note->type] ?? 'secondary' }} ms-1"
                                  style="font-size:0.7rem;">{{ ucfirst($note->type) }}</span>
                        </div>
                        <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0 text-muted" style="font-size:0.9rem;">{{ $note->note }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Actions</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.guests.edit',$guest) }}" class="btn btn-outline-primary">Edit Guest</a>
                <form action="{{ route('admin.guests.destroy',$guest) }}" method="POST" onsubmit="return confirm('Delete this guest?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100">Delete Guest</button>
                </form>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.guests.index') }}" class="btn btn-secondary mt-3">← Back to Guests</a>
@endsection
