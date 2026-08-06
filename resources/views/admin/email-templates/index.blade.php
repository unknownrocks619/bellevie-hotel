@extends('layouts.admin')
@section('page-title', 'Email Templates')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header"><i class="bi bi-envelope-paper me-2"></i>Email Templates</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>Name</th>
                    <th>Subject</th>
                    <th style="width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                <tr>
                    <td>{{ $template->name }}</td>
                    <td class="text-muted">{{ $template->subject }}</td>
                    <td>
                        <a href="{{ route('admin.email-templates.edit', $template) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-4">No email templates found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
