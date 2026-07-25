@extends('layouts.admin')
@section('page-title', 'Menu Items')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.restaurant.edit') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <form class="d-flex gap-2" method="GET" action="{{ route('admin.restaurant.menu-items.index') }}">
            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $id => $name)
                <option value="{{ $id }}" {{ (string) request('category_id') === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search…" value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <a href="{{ route('admin.restaurant.menu-items.create') }}" class="btn btn-sm text-white" style="background:#C9A227;border:none;">
        <i class="bi bi-plus-circle me-1"></i>New Menu Item
    </a>
</div>

<p class="text-muted small mb-3"><i class="bi bi-arrows-move me-1"></i>Drag items by the handle to reorder them within a category.</p>

@forelse($groups as $category)
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ $category->name }}</span>
        <span class="text-muted small">{{ $category->items->count() }} item(s)</span>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush sortable-list" id="sortable-cat-{{ $category->id }}">
            @forelse($category->items as $item)
            <li class="list-group-item d-flex align-items-center gap-3" data-id="{{ $item->id }}">
                <span class="drag-handle text-muted" style="cursor:grab;"><i class="bi bi-grip-vertical"></i></span>

                @if($item->image_url)
                <img src="{{ $item->image_url }}" alt="" style="width:44px;height:34px;object-fit:cover;border-radius:4px;">
                @else
                <div style="width:44px;height:34px;border-radius:4px;background:#C9A22720;display:flex;align-items:center;justify-content:center;color:#C9A227;flex-shrink:0;">
                    <i class="bi bi-egg-fried"></i>
                </div>
                @endif

                <div class="flex-grow-1">
                    <div class="fw-semibold">
                        {{ $item->name }}
                        @if($item->is_featured)
                        <i class="bi bi-star-fill" style="color:#C9A227;font-size:0.72rem;" title="Featured"></i>
                        @endif
                        @if(!$item->show_price)
                        <span class="badge bg-secondary" style="font-size:.65rem;" title="Price hidden from visitors">Price hidden</span>
                        @endif
                    </div>
                    <small class="text-muted">{{ Str::limit($item->description, 70) }}</small>
                </div>

                <div class="text-muted small" style="width:80px;">
                    {{ $item->price !== null ? '$'.number_format((float) $item->price, 2) : '—' }}
                </div>

                <form action="{{ route('admin.restaurant.menu-items.toggle-status', $item) }}" method="POST" class="d-inline">
                    @csrf @method('POST')
                    <button type="submit" class="btn btn-sm {{ $item->is_active ? 'text-white' : 'btn-outline-secondary' }}"
                            style="{{ $item->is_active ? 'background:#C9A227;border:none;' : '' }}">
                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </form>

                <a href="{{ route('admin.restaurant.menu-items.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="{{ route('admin.restaurant.menu-items.destroy', $item) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this menu item?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Del</button>
                </form>
            </li>
            @empty
            <li class="list-group-item text-center text-muted py-4">No menu items in this category yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        No menu categories yet. <a href="{{ route('admin.restaurant.categories.create') }}">Add one first</a>, then add menu items.
    </div>
</div>
@endforelse

@push('page_script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const REORDER_URL = '{{ route('admin.restaurant.menu-items.reorder') }}';

document.querySelectorAll('.sortable-list').forEach(function (el) {
    Sortable.create(el, {
        animation: 180,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost',
        onEnd: function () {
            const items = [...el.querySelectorAll('li[data-id]')].map(function (li) {
                return { id: parseInt(li.dataset.id) };
            });
            fetch(REORDER_URL, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ items: items }),
            }).catch(function () { alert('Failed to save order — please try again.'); });
        },
    });
});
</script>
<style>
.sortable-ghost { opacity: .45; background: #fdf8ea !important; }
</style>
@endpush

@endsection
