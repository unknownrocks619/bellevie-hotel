@extends('layouts.admin')
@section('page-title', 'Edit ' . $event->type_label)
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Edit {{ $event->type_label }}: {{ $event->title }}</span>
        <a href="{{ route('events.show', $event) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-box-arrow-up-right me-1"></i>View
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.events.update', $event) }}" method="POST">
            @csrf @method('PUT')
            @include('admin.events._form')
            <button class="btn text-white" style="background:#C9A227;border:none;">Save Changes</button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Back to List</a>
        </form>
    </div>
</div>
@endsection
