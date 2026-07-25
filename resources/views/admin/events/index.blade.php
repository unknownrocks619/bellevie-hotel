@extends('layouts.admin')
@section('page-title', 'Events & Conferences')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Toolbar --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        {{-- Type tabs --}}
        <a href="{{ route('admin.events.index') }}"
           class="btn btn-sm {{ !request('type') ? 'text-white' : 'btn-outline-secondary' }}"
           style="{{ !request('type') ? 'background:#C9A227;border:none;' : '' }}">
            All
        </a>
        @foreach($types as $slug => $label)
        <a href="{{ route('admin.events.index', ['type' => $slug]) }}"
           class="btn btn-sm {{ request('type') === $slug ? 'text-white' : 'btn-outline-secondary' }}"
           style="{{ request('type') === $slug ? 'background:#C9A227;border:none;' : '' }}">
            {{ $label }}s
        </a>
        @endforeach
    </div>

    <div class="d-flex gap-2">
        <form class="d-flex gap-2" method="GET" action="{{ route('admin.events.index') }}">
            @if(request('type'))
            <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search…" value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('admin.events.create', ['type' => 'event']) }}" class="btn btn-sm text-white" style="background:#C9A227;border:none;">
            <i class="bi bi-plus-circle me-1"></i>New Event
        </a>
        <a href="{{ route('admin.events.create', ['type' => 'conference']) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-people me-1"></i>New Conference
        </a>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="width:64px;"></th>
                    <th>Title</th>
                    <th style="width:120px;">Type</th>
                    <th style="width:170px;">Date</th>
                    <th style="width:160px;">Venue</th>
                    <th style="width:90px;">Status</th>
                    <th style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr>
                    <td>
                        @if($event->image_url)
                        <img src="{{ $event->image_url }}" alt="" style="width:48px;height:36px;object-fit:cover;border-radius:4px;">
                        @else
                        <div style="width:48px;height:36px;border-radius:4px;background:#C9A22720;display:flex;align-items:center;justify-content:center;color:#C9A227;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">
                            {{ Str::limit($event->title, 60) }}
                            @if($event->is_featured)
                            <i class="bi bi-star-fill" style="color:#C9A227;font-size:0.75rem;" title="Featured"></i>
                            @endif
                        </div>
                        <small class="text-muted">{{ Str::limit($event->excerpt ?: strip_tags($event->description), 70) }}</small>
                    </td>
                    <td>
                        <span class="badge rounded-pill" style="background:#C9A22720;color:#C9A227;border:1px solid #C9A22740;font-size:0.72rem;">
                            {{ $event->type_label }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $event->date_range ?: '—' }}</td>
                    <td class="text-muted small">{{ Str::limit($event->venue, 24) ?: '—' }}</td>
                    <td>
                        @if($event->is_active)
                            <span class="badge text-white" style="background:#C9A227;">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this {{ strtolower($event->type_label) }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Del</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-event" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        No events or conferences yet.
                        <a href="{{ route('admin.events.create') }}">Add the first one</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($events->hasPages())
<div class="mt-3">
    {{ $events->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection
