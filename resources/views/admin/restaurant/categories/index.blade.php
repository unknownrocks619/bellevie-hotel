@extends('layouts.admin')
@section('page-title', 'Menu Categories')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <a href="{{ route('admin.restaurant.edit') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Restaurant Page
    </a>
    <a href="{{ route('admin.restaurant.categories.create') }}" class="btn btn-sm text-white" style="background:#C9A227;border:none;">
        <i class="bi bi-plus-circle me-1"></i>New Category
    </a>
</div>

<p class="text-muted small mb-2"><i class="bi bi-arrows-move me-1"></i>Drag by the handle to reorder categories.</p>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="width:36px;"></th>
                    <th>Name</th>
                    <th style="width:100px;">Items</th>
                    <th style="width:90px;">Status</th>
                    <th style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody id="sortable-categories">
                @forelse($categories as $category)
                <tr data-id="{{ $category->id }}">
                    <td class="drag-handle text-muted" style="cursor:grab;"><i class="bi bi-grip-vertical"></i></td>
                    <td>
                        <div class="fw-semibold">{{ $category->name }}</div>
                        <small class="text-muted">{{ Str::limit($category->description, 60) }}</small>
                    </td>
                    <td class="text-muted">{{ $category->items_count }}</td>
                    <td>
                        @if($category->is_active)
                            <span class="badge text-white" style="background:#C9A227;">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.restaurant.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.restaurant.categories.destroy', $category) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this category and all its menu items?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Del</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        No menu categories yet. <a href="{{ route('admin.restaurant.categories.create') }}">Add the first one</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('page_script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const el = document.getElementById('sortable-categories');
    if (!el) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const reorderUrl = '{{ route('admin.restaurant.categories.reorder') }}';

    Sortable.create(el, {
        animation: 180,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost',
        onEnd: function () {
            const items = [...el.querySelectorAll('tr[data-id]')].map(function (tr) {
                return { id: parseInt(tr.dataset.id) };
            });
            fetch(reorderUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ items: items }),
            }).catch(function () { alert('Failed to save order — please try again.'); });
        },
    });
})();
</script>
<style>
.sortable-ghost { opacity: .45; background: #fdf8ea !important; }
</style>
@endpush

@endsection
