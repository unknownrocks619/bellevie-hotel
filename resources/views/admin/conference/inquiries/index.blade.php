@extends('layouts.admin')
@section('page-title', 'Conference Inquiries')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.conference-inquiries.index') }}"
           class="btn btn-sm {{ !request('status') ? 'text-white' : 'btn-outline-secondary' }}"
           style="{{ !request('status') ? 'background:#C9A227;border:none;' : '' }}">All</a>
        @foreach($statuses as $slug => $label)
        <a href="{{ route('admin.conference-inquiries.index', ['status' => $slug]) }}"
           class="btn btn-sm {{ request('status') === $slug ? 'text-white' : 'btn-outline-secondary' }}"
           style="{{ request('status') === $slug ? 'background:#C9A227;border:none;' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
    <div class="d-flex gap-2">
        <form class="d-flex gap-2" method="GET" action="{{ route('admin.conference-inquiries.index') }}">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search…" value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('admin.conference.edit') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Page
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th style="width:110px;">Event Date</th>
                    <th style="width:80px;">Guests</th>
                    <th style="width:130px;">Received</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inquiry)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $inquiry->name }}</div>
                        <small class="text-muted">{{ $inquiry->email }}</small>
                    </td>
                    <td class="text-muted small">{{ $inquiry->company ?: '—' }}</td>
                    <td class="text-muted small">{{ $inquiry->event_date?->format('d M Y') ?? '—' }}</td>
                    <td class="text-muted small">{{ $inquiry->guests_count ?? '—' }}</td>
                    <td class="text-muted small">{{ $inquiry->created_at->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $inquiry->status === 'new' ? 'text-white' : ($inquiry->status === 'contacted' ? 'bg-info text-dark' : 'bg-secondary') }}"
                              style="{{ $inquiry->status === 'new' ? 'background:#C9A227;' : '' }}">
                            {{ $inquiry->status_label }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.conference-inquiries.show', $inquiry) }}" class="btn btn-sm btn-outline-primary">View</a>
                        <form action="{{ route('admin.conference-inquiries.destroy', $inquiry) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this inquiry?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Del</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">No conference inquiries yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($inquiries->hasPages())
<div class="mt-3">{{ $inquiries->links('pagination::bootstrap-5') }}</div>
@endif

@endsection
