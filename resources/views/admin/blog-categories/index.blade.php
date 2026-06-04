@extends('layouts.admin')
@section('page-title', 'Blog Categories')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Blog Categories</h4>
    <a href="{{ route('admin.blog-categories.create') }}" class="btn text-white" style="background:#C9A227;border:none;">
        <i class="bi bi-plus-circle me-1"></i>New Category
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Posts</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td class="fw-semibold">{{ $cat->name }}</td>
                    <td><code>{{ $cat->slug }}</code></td>
                    <td><span class="badge bg-secondary">{{ $cat->posts_count }}</span></td>
                    <td>
                        @if($cat->is_active)
                            <span class="badge text-white" style="background:#C9A227;">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.blog-categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="{{ route('blog.category', $cat->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                        <form action="{{ route('admin.blog-categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
