@extends('layouts.admin')
@section('page-title', 'Menu Management')
@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div id="reorderToast"
        style="
    display:none; position:fixed; bottom:24px; right:24px; z-index:9999;
    background:#1a1a2e; color:#fff; padding:10px 18px; border-radius:8px;
    font-size:0.82rem; box-shadow:0 4px 16px rgba(0,0,0,.25); align-items:center; gap:8px;">
        <span id="reorderToastMsg">Order saved</span>
    </div>

    <div class="row g-4">

        {{-- Header Menu --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-layout-text-window-reverse me-2" style="color:#C9A227;"></i>Header Menu</span>
                    <a href="{{ route('admin.menus.create', ['menu' => 'header']) }}" class="btn btn-sm text-white"
                        style="background:#C9A227;border:none;">
                        <i class="bi bi-plus-circle me-1"></i>Add Item
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($headerItems->count() > 0)
                        <ul id="sortable-header" class="list-group list-group-flush" style="min-height:60px;">
                            @foreach ($headerItems as $item)
                                <li class="list-group-item d-flex align-items-center gap-3 px-3 py-3"
                                    data-id="{{ $item->id }}" style="user-select:none;">
                                    <span class="drag-handle"
                                        style="color:#ccc; cursor:grab; font-size:1.1rem; flex-shrink:0;">
                                        <i class="bi bi-grip-vertical"></i>
                                    </span>
                                    <div style="flex:1; min-width:0;">
                                        <div class="fw-semibold" style="font-size:0.9rem;">{{ $item->title }}</div>
                                        <small class="text-muted text-truncate d-block" style="max-width:220px;">
                                            @if (str_contains($item->route_name ?? '', 'http'))
                                                Route : <a href="{{ $item->route_name }}"
                                                    target="_blank">{{ $item->route_name }}</a>
                                            @elseif($item->route_name)
                                                Route : {{ $item->route_name }}
                                            @else
                                                Url: {{ $item->url }}
                                            @endif
                                        </small>
                                    </div>
                                    @if (!$item->is_active)
                                        <span class="badge bg-secondary" style="font-size:0.68rem;">Inactive</span>
                                    @endif
                                    <div class="d-flex gap-1 flex-shrink-0">
                                        <a href="{{ route('admin.menus.edit', $item) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.menus.destroy', $item) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this item?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <p class="text-muted text-center py-2 mb-0" style="font-size:0.75rem;">
                            <i class="bi bi-arrows-move me-1"></i>Drag rows to reorder
                        </p>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-menu-button-wide" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            No header menu items yet.
                            <a href="{{ route('admin.menus.create', ['menu' => 'header']) }}">Add the first one</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer Menu --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-layout-text-window me-2" style="color:#C9A227;"></i>Footer Menu</span>
                    <a href="{{ route('admin.menus.create', ['menu' => 'footer']) }}" class="btn btn-sm text-white"
                        style="background:#C9A227;border:none;">
                        <i class="bi bi-plus-circle me-1"></i>Add Item
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($footerItems->count() > 0)
                        <ul id="sortable-footer" class="list-group list-group-flush" style="min-height:60px;">
                            @foreach ($footerItems as $item)
                                <li class="list-group-item d-flex align-items-center gap-3 px-3 py-3"
                                    data-id="{{ $item->id }}" style="user-select:none;">
                                    <span class="drag-handle"
                                        style="color:#ccc; cursor:grab; font-size:1.1rem; flex-shrink:0;">
                                        <i class="bi bi-grip-vertical"></i>
                                    </span>
                                    <div style="flex:1; min-width:0;">
                                        <div class="fw-semibold" style="font-size:0.9rem;">{{ $item->title }}</div>
                                        <small class="text-muted text-truncate d-block" style="max-width:220px;">
                                            {{ $item->route_name ? 'Route: ' . $item->route_name : 'URL: ' . ($item->attributes['url'] ?? $item->url) }}
                                        </small>
                                    </div>
                                    @if (!$item->is_active)
                                        <span class="badge bg-secondary" style="font-size:0.68rem;">Inactive</span>
                                    @endif
                                    <div class="d-flex gap-1 flex-shrink-0">
                                        <a href="{{ route('admin.menus.edit', $item) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.menus.destroy', $item) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this item?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <p class="text-muted text-center py-2 mb-0" style="font-size:0.75rem;">
                            <i class="bi bi-arrows-move me-1"></i>Drag rows to reorder
                        </p>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-menu-button-wide" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            No footer menu items yet.
                            <a href="{{ route('admin.menus.create', ['menu' => 'footer']) }}">Add the first one</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const REORDER_URL = '{{ route('admin.menus.reorder') }}';

        function showToast(msg, isError) {
            const toast = document.getElementById('reorderToast');
            document.getElementById('reorderToastMsg').textContent = msg;
            toast.style.background = isError ? '#c0392b' : '#155724';
            toast.style.display = 'flex';
            clearTimeout(toast._timer);
            toast._timer = setTimeout(() => {
                toast.style.display = 'none';
            }, 2500);
        }

        function initSortable(listId) {
            const el = document.getElementById(listId);
            if (!el) return;

            Sortable.create(el, {
                animation: 180,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd() {
                    const items = [...el.querySelectorAll('li[data-id]')].map(li => ({
                        id: parseInt(li.dataset.id),
                    }));

                    fetch(REORDER_URL, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                items
                            }),
                        })
                        .then(r => r.ok ? r.json() : Promise.reject(r.status))
                        .then(() => showToast('✓ Order saved'))
                        .catch(() => showToast('✗ Failed to save — try again', true));
                },
            });
        }

        initSortable('sortable-header');
        initSortable('sortable-footer');
    </script>

    <style>
        .sortable-ghost {
            opacity: 0.45;
            background: #fdf8ea !important;
            border-left: 3px solid #C9A227 !important;
        }

        .list-group-item {
            transition: background .1s;
        }

        .list-group-item:hover {
            background: #fafafa;
        }

        .drag-handle:active {
            cursor: grabbing;
        }
    </style>

@endsection
