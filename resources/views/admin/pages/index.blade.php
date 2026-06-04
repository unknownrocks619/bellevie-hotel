@extends('layouts.admin')
@section('page-title', 'Pages')
@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary" style="background:#C9A227;border:none;"><i class="bi bi-plus-circle"></i> New Page</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm datatable">
                <thead><tr>
                    <th>Title</th><th>Slug</th><th>Active</th><th>Sort</th><th>Updated At</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($pages as $page)
                <tr>
                    <td>{{ $page->title }}</td>
                    <td><code>{{ $page->slug }}</code></td>
                    <td>
                        <span class="badge bg-{{ $page->is_active ? 'success' : 'secondary' }}">{{ $page->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>
                    <td>{{ $page->sort_order }}</td>
                    <td>{{ $page->updated_at->format('M d, Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.pages.edit',$page) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="{{ route('admin.builder.editPage',$page) }}"
                           class="btn btn-sm btn-outline-warning"
                           title="Page Builder{{ $page->use_builder ? ' (active)' : '' }}">
                            <i class="bi bi-grid-1x2"></i>
                            Builder{{ $page->use_builder ? ' ✓' : '' }}
                        </a>
                        @if($page->is_active)
                        <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Preview"><i class="bi bi-eye"></i></a>
                        @endif
                        <form action="{{ route('admin.pages.destroy',$page) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this page?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No pages found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($pages->hasPages())
        {{ $pages->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
@endsection
