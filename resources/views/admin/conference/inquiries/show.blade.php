@extends('layouts.admin')
@section('page-title', 'Inquiry from ' . $inquiry->name)
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">Inquiry Details</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8">{{ $inquiry->name }}</dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd>
                    <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $inquiry->phone ?: '—' }}</dd>
                    <dt class="col-sm-4">Company</dt><dd class="col-sm-8">{{ $inquiry->company ?: '—' }}</dd>
                    <dt class="col-sm-4">Event Date</dt><dd class="col-sm-8">{{ $inquiry->event_date?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-sm-4">Guests</dt><dd class="col-sm-8">{{ $inquiry->guests_count ?? '—' }}</dd>
                    <dt class="col-sm-4">Received</dt><dd class="col-sm-8">{{ $inquiry->created_at->format('d M Y, g:i A') }}</dd>
                </dl>
                <hr>
                <h6>Message</h6>
                <p style="white-space:pre-wrap;">{{ $inquiry->message }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Manage</div>
            <div class="card-body">
                <form action="{{ route('admin.conference-inquiries.update', $inquiry) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach($statuses as $slug => $label)
                            <option value="{{ $slug }}" {{ $inquiry->status === $slug ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="4">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea>
                    </div>
                    <button class="btn text-white" style="background:#C9A227;border:none;">Save</button>
                    <a href="{{ route('admin.conference-inquiries.index') }}" class="btn btn-secondary">Back to List</a>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
