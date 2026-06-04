@extends('layouts.admin')
@section('page-title', 'Gallery')
@section('content')

@php
    $cloudName    = config('cloudinary.cloud_name') ?: Str::after(config('cloudinary.url', ''), '@');
    $uploadPreset = config('cloudinary.upload_preset', '');
    $uploadFolder = 'bellevie_hotel/gallery';
@endphp

{{-- ── Toolbar ── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn text-white" style="background:#C9A227;border:none;"
                onclick="galleryOpenWidget()">
            <i class="bi bi-cloud-upload me-1"></i>Upload Images
        </button>

        <button type="button" class="btn btn-outline-secondary"
                onclick="openLibraryModal()">
            <i class="bi bi-collection me-1"></i>Select from Library
        </button>

        <select id="categoryFilter" class="form-select form-select-sm" style="width:auto;"
                onchange="galleryFilter()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>

        <div class="input-group input-group-sm" style="width:220px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="gallerySearch" class="form-control" placeholder="Search title…"
                   value="{{ request('search') }}" oninput="galleryFilter()">
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span id="selectedCount" class="text-muted small d-none">0 selected</span>
        <button type="button" class="btn btn-sm btn-outline-danger d-none" id="btnBulkDelete"
                onclick="galleryBulkDelete()">
            <i class="bi bi-trash me-1"></i>Delete Selected
        </button>
    </div>
</div>

{{-- ── Section header ── --}}
<div class="d-flex align-items-center gap-2 mb-2">
    <h6 class="mb-0 fw-bold" style="color:#0D1B2A;">
        <i class="bi bi-images me-1" style="color:#C9A227;"></i>Gallery
    </h6>
    <span class="badge bg-secondary">{{ $galleries->total() }} items</span>
    @if(!request('category') && !request('search') && $galleries->count() > 1)
    <small class="text-muted ms-1"><i class="bi bi-arrows-move me-1"></i>Drag to reorder</small>
    @endif
</div>

{{-- ── Gallery grid (sortable) ── --}}
<div class="row g-3" id="galleryGrid">
    @forelse($galleries as $gallery)
    @php $imgUrl = $gallery->image_url; $imgThumb = $gallery->image_thumb; @endphp
    <div class="col-6 col-md-3 col-lg-2 gallery-item" data-id="{{ $gallery->id }}">
        <div class="card h-100 gallery-card" style="cursor:pointer;"
             onclick="galleryToggleSelect(this, {{ $gallery->id }})">

            <div class="gallery-check position-absolute"
                 style="top:6px;left:6px;z-index:2;display:none;">
                <div style="width:22px;height:22px;background:#C9A227;border-radius:4px;
                            display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-check text-white" style="font-size:14px;"></i>
                </div>
            </div>

            {{-- Drag handle (top-right) --}}
            <div class="drag-handle position-absolute"
                 style="top:6px;right:6px;z-index:2;width:24px;height:24px;border-radius:4px;
                        background:rgba(0,0,0,.35);color:#fff;display:flex;align-items:center;
                        justify-content:center;cursor:grab;font-size:0.9rem;"
                 title="Drag to reorder" onclick="event.stopPropagation()">
                <i class="bi bi-grip-vertical"></i>
            </div>

            @if($imgThumb ?: $imgUrl)
            <img src="{{ $imgThumb ?: $imgUrl }}" alt="{{ $gallery->title }}"
                 class="card-img-top" style="height:130px;object-fit:cover;">
            @else
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                 style="height:130px;">
                <i class="bi bi-image text-muted" style="font-size:2rem;"></i>
            </div>
            @endif

            <div class="card-body p-2">
                <p class="mb-0 text-truncate small fw-semibold">{{ $gallery->title ?: 'Untitled' }}</p>
                <small class="text-muted">{{ $gallery->category ?? '—' }}</small>
            </div>

            <div class="card-footer p-1 bg-white border-top d-flex gap-1">
                <a href="javascript:void(0)" class="btn btn-xs btn-outline-secondary flex-fill py-1"
                   style="font-size:0.7rem;"
                   onclick="event.stopPropagation();galleryEdit(
                       {{ $gallery->id }},
                       '{{ addslashes($imgUrl) }}',
                       '{{ addslashes($gallery->title ?? '') }}',
                       '{{ addslashes($gallery->description ?? '') }}',
                       '{{ addslashes($gallery->category ?? '') }}',
                       {{ $gallery->sort_order ?? 0 }},
                       {{ $gallery->is_active ? 1 : 0 }})">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('admin.gallery.destroy', $gallery) }}" method="POST"
                      onsubmit="return confirm('Remove this image from gallery?')"
                      onclick="event.stopPropagation()">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger py-1" style="font-size:0.7rem;">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 text-muted">
            <i class="bi bi-images" style="font-size:3rem;"></i>
            <p class="mt-3">No images yet. Use <strong>Upload Images</strong> to upload new ones, or <strong>Select from Library</strong> to add from Cloudinary.</p>
        </div>
    </div>
    @endforelse
</div>

@if(method_exists($galleries, 'hasPages') && $galleries->hasPages())
<div class="mt-4">{{ $galleries->links('pagination::bootstrap-5') }}</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════════
     CLOUDINARY LIBRARY MODAL
═══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="libraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:2px solid #C9A22730;">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title mb-0">
                        <i class="bi bi-cloud me-2" style="color:#C9A227;"></i>Cloudinary Library
                    </h5>
                    <span class="badge" style="background:#C9A22720;color:#C9A227;border:1px solid #C9A22740;font-size:0.7rem;">All images</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#f8f9fa;">
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Click any image to add it to your gallery.
                    <span style="color:#28a745;font-weight:600;"><i class="bi bi-check-circle-fill me-1"></i>Green</span> = already in gallery.
                </p>
                <div id="cloudinaryGrid" class="row g-3">
                    <div class="col-12 text-center py-5 text-muted" id="cloudinaryLoading">
                        <div class="spinner-border me-2" style="color:#C9A227;width:1.5rem;height:1.5rem;"></div>
                        Loading images from Cloudinary…
                    </div>
                </div>
                <div class="text-center mt-4" id="cloudinaryLoadMore" style="display:none;">
                    <button class="btn btn-outline-secondary" onclick="loadCloudinaryNext()">
                        <i class="bi bi-arrow-down-circle me-1"></i>Load More
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <small class="text-muted me-auto">Images already in your gallery cannot be added again.</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ── MODALS ── --}}

{{-- Upload modal (after Cloudinary widget closes) --}}
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.gallery.store') }}" method="POST" id="galleryStoreForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Save Uploaded Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="uploadedPreviews" class="d-flex flex-wrap gap-2 mb-3"></div>
                    <div id="uploadedInputs"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control" list="galCategoryList"
                               placeholder="e.g. Rooms, Dining, Pool…" required>
                        <datalist id="galCategoryList">
                            @foreach($categories as $cat)<option value="{{ $cat }}">@endforeach
                            <option value="Rooms"><option value="Lobby">
                            <option value="Dining"><option value="Pool & Spa">
                            <option value="Events"><option value="Exterior">
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <small class="text-muted">(optional — uses filename if blank)</small></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Deluxe Room View">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background:#C9A227;border:none;">
                        <i class="bi bi-floppy me-1"></i>Save to Gallery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Import from Cloudinary Library (assign category before saving) --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2" style="color:#C9A227;"></i>Add to Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="importPreviewImg" src="" alt=""
                         style="max-height:180px;max-width:100%;object-fit:cover;border-radius:8px;border:2px solid #C9A22740;">
                </div>
                <p class="text-muted small mb-3" id="importFilename"></p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <input type="text" id="importCategory" class="form-control" list="importCategoryList"
                           placeholder="e.g. Rooms, Dining, Pool…">
                    <datalist id="importCategoryList">
                        @foreach($categories as $cat)<option value="{{ $cat }}">@endforeach
                        <option value="Rooms"><option value="Lobby">
                        <option value="Dining"><option value="Pool & Spa">
                        <option value="Events"><option value="Exterior">
                    </datalist>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title <small class="text-muted">(optional)</small></label>
                    <input type="text" id="importTitle" class="form-control" placeholder="Leave blank to use filename">
                </div>
                <div id="importError" class="alert alert-danger mt-2 d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" style="background:#C9A227;border:none;"
                        id="importConfirmBtn" onclick="submitImport()">
                    <i class="bi bi-plus-circle me-1"></i>Add to Gallery
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Inline edit modal --}}
<div class="modal fade" id="galleryEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="galleryEditForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Gallery Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="editPreviewImg" src="" alt=""
                             style="max-height:160px;max-width:100%;border-radius:8px;object-fit:cover;display:none;">
                        <div id="editPreviewEmpty" class="d-flex align-items-center justify-content-center bg-light rounded"
                             style="height:100px;display:none!important;">
                            <i class="bi bi-image text-muted" style="font-size:2rem;"></i>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" id="editTitle" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="editCategory" class="form-control"
                               list="galInlineList" required>
                        <datalist id="galInlineList">
                            @foreach($categories as $cat)<option value="{{ $cat }}">@endforeach
                        </datalist>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" value="1">
                        <label class="form-check-label" for="editIsActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background:#C9A227;border:none;">
                        <i class="bi bi-floppy me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk delete form --}}
<form action="{{ route('admin.gallery.bulkDestroy') }}" method="POST" id="bulkDeleteForm">
    @csrf @method('DELETE')
    <div id="bulkIds"></div>
</form>

{{-- Toast --}}
<div id="galToast" style="
    display:none; position:fixed; bottom:24px; right:24px; z-index:9999;
    padding:10px 18px; border-radius:8px; font-size:0.82rem;
    box-shadow:0 4px 16px rgba(0,0,0,.25); color:#fff;">
</div>

<style>
.gallery-card { transition: transform .15s, box-shadow .15s; }
.gallery-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.12); }
.gallery-card.is-selected { outline: 3px solid #C9A227; }
.gallery-item.sortable-ghost { opacity: .4; }
.gallery-item.sortable-ghost .gallery-card { background: #fdf8ea; border: 2px dashed #C9A227; }
.btn-xs { padding: .15rem .4rem; font-size: .72rem; }

.cl-card {
    border-radius: 8px; overflow: hidden; border: 2px solid transparent;
    background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.08);
    transition: border-color .15s, box-shadow .15s; cursor: pointer;
}
.cl-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.14); border-color: #C9A22760; }
.cl-card.in-gallery { border-color: #28a74550; cursor: default; }
.cl-card.in-gallery:hover { box-shadow: 0 1px 4px rgba(0,0,0,.08); border-color: #28a74550; }
.cl-badge {
    position: absolute; top: 6px; right: 6px; font-size: .65rem;
    padding: 2px 7px; border-radius: 20px; font-weight: 600;
}
#importModal { z-index: 1060; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>
<script>
(function () {
    const CSRF        = document.querySelector('meta[name="csrf-token"]').content;
    const LIBRARY_URL = '{{ route('admin.gallery.cloudinaryLibrary') }}';
    const IMPORT_URL  = '{{ route('admin.gallery.import') }}';
    const REORDER_URL = '{{ route('admin.gallery.reorder') }}';

    let pendingImages = [];
    let selected      = [];
    let nextCursor    = null;
    let libraryLoaded = false;
    let importing     = null;

    // ── Toast ─────────────────────────────────────────────────────────────────
    function toast(msg, ok = true) {
        const el = document.getElementById('galToast');
        el.textContent      = msg;
        el.style.background = ok ? '#155724' : '#c0392b';
        el.style.display    = 'block';
        clearTimeout(el._t);
        el._t = setTimeout(() => el.style.display = 'none', 3000);
    }

    // ── Sortable grid ─────────────────────────────────────────────────────────
    const grid = document.getElementById('galleryGrid');
    if (grid && grid.querySelector('[data-id]')) {
        Sortable.create(grid, {
            animation: 180,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            filter: '.gallery-check',   // don't drag when clicking checkbox
            onEnd() {
                const items = [...grid.querySelectorAll('.gallery-item[data-id]')].map(el => ({
                    id: parseInt(el.dataset.id),
                }));
                fetch(REORDER_URL, {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json' },
                    body: JSON.stringify({ items }),
                })
                .then(r => r.ok ? r.json() : Promise.reject(r.status))
                .then(() => toast('✓ Order saved'))
                .catch(() => toast('✗ Failed to save order', false));
            },
        });
    }

    // ── Upload widget ─────────────────────────────────────────────────────────
    window.galleryOpenWidget = function () {
        const cloudName = @json($cloudName);
        const preset    = @json($uploadPreset);
        const folder    = @json($uploadFolder);

        if (!cloudName || !preset) {
            alert('Cloudinary upload preset not configured.\nSet CLOUDINARY_UPLOAD_PRESET in your .env file.');
            return;
        }

        window.cloudinary.openUploadWidget({
            cloud_name: cloudName, upload_preset: preset, folder, multiple: true,
            sources: ['local','url','camera'],
            styles: { palette: { action:'#C9A227', link:'#C9A227' } }
        }, function (error, result) {
            if (error) { console.error(error); return; }
            if (result.event === 'success') galleryOnUpload(result.info);
            if (result.event === 'close' && pendingImages.length > 0) {
                new bootstrap.Modal(document.getElementById('uploadModal')).show();
            }
        }).open();
    };

    function galleryOnUpload(info) {
        // Save to images table first (idempotent)
        fetch('/admin/images/save', {
            method: 'POST', credentials: 'same-origin',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json' },
            body: JSON.stringify(info)
        })
        .then(r => r.ok ? r.json() : Promise.reject(r.status))
        .then(img => {
            pendingImages.push(img);
            const thumb = document.createElement('div');
            thumb.innerHTML = `<div style="text-align:center;">
                <img src="${img.thumb||img.url}"
                     style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:2px solid #C9A227;">
                <small class="d-block text-truncate mt-1" style="width:80px;font-size:.65rem;">${img.filename}</small>
            </div>`;
            document.getElementById('uploadedPreviews').appendChild(thumb);
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'image_ids[]'; inp.value = img.id;
            document.getElementById('uploadedInputs').appendChild(inp);
        })
        .catch(e => console.error('Save error:', e));
    }

    document.getElementById('uploadModal').addEventListener('hidden.bs.modal', function () {
        pendingImages = [];
        document.getElementById('uploadedPreviews').innerHTML = '';
        document.getElementById('uploadedInputs').innerHTML   = '';
    });

    // ── Cloudinary Library modal ──────────────────────────────────────────────
    window.openLibraryModal = function () {
        new bootstrap.Modal(document.getElementById('libraryModal')).show();
        if (!libraryLoaded) loadCloudinaryLibrary();
    };

    function loadCloudinaryLibrary(cursor = null) {
        const grid    = document.getElementById('cloudinaryGrid');
        const loading = document.getElementById('cloudinaryLoading');

        if (!cursor) {
            grid.innerHTML = '';
            loading.style.display = 'block';
            grid.appendChild(loading);
        }
        document.getElementById('cloudinaryLoadMore').style.display = 'none';

        const url = LIBRARY_URL + (cursor ? '?next_cursor=' + encodeURIComponent(cursor) : '');

        fetch(url, { credentials:'same-origin', headers:{ 'Accept':'application/json' } })
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(data => {
            loading.style.display = 'none';
            libraryLoaded = true;

            if (data.error) {
                const errEl = document.createElement('div');
                errEl.className = 'col-12';
                errEl.innerHTML = `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>${data.error}</div>`;
                grid.appendChild(errEl);
                return;
            }

            if (!data.items?.length && !cursor) {
                const emptyEl = document.createElement('div');
                emptyEl.className = 'col-12 text-center py-5 text-muted';
                emptyEl.innerHTML = `<i class="bi bi-cloud-slash" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>No images found in Cloudinary.`;
                grid.appendChild(emptyEl);
                return;
            }

            data.items.forEach(item => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3 col-lg-2';
                col.innerHTML = buildCloudinaryCard(item);
                grid.appendChild(col);
            });

            nextCursor = data.next_cursor || null;
            document.getElementById('cloudinaryLoadMore').style.display = nextCursor ? 'block' : 'none';
        })
        .catch(err => {
            loading.style.display = 'none';
            const errEl = document.createElement('div');
            errEl.className = 'col-12';
            errEl.innerHTML = `<div class="alert alert-danger">Failed to load Cloudinary library. Please check your API credentials.</div>`;
            grid.appendChild(errEl);
            console.error(err);
        });
    }

    window.loadCloudinaryNext = function () {
        if (nextCursor) loadCloudinaryLibrary(nextCursor);
    };

    function buildCloudinaryCard(item) {
        const kb = item.bytes ? Math.round(item.bytes / 1024) + ' KB' : '';

        if (item.in_gallery) {
            return `<div class="cl-card in-gallery position-relative" title="${item.public_id}">
                <img src="${item.thumb_url}" alt="${item.filename}"
                     style="width:100%;height:130px;object-fit:cover;opacity:.7;">
                <span class="cl-badge" style="background:#28a745;color:#fff;">
                    <i class="bi bi-check-circle-fill me-1"></i>In Gallery
                </span>
                <div class="p-2">
                    <p class="mb-0 text-truncate text-muted" style="font-size:.72rem;">${item.filename}</p>
                    ${kb ? `<small class="text-muted" style="font-size:.65rem;">${kb}</small>` : ''}
                </div>
            </div>`;
        }

        return `<div class="cl-card position-relative" title="Click to add to gallery"
                     onclick='openImportModal(${JSON.stringify(item)})'>
            <img src="${item.thumb_url}" alt="${item.filename}"
                 style="width:100%;height:130px;object-fit:cover;">
            <span class="cl-badge" style="background:#C9A227;color:#fff;">
                <i class="bi bi-plus me-1"></i>Add
            </span>
            <div class="p-2">
                <p class="mb-0 text-truncate fw-semibold" style="font-size:.72rem;">${item.filename}</p>
                ${kb ? `<small class="text-muted" style="font-size:.65rem;">${kb}</small>` : ''}
            </div>
        </div>`;
    }

    // ── Import modal ──────────────────────────────────────────────────────────
    window.openImportModal = function (item) {
        importing = item;
        document.getElementById('importPreviewImg').src       = item.thumb_url;
        document.getElementById('importFilename').textContent = item.filename + (item.format ? '  ·  ' + item.format.toUpperCase() : '');
        document.getElementById('importCategory').value  = '';
        document.getElementById('importTitle').value     = '';
        document.getElementById('importError').classList.add('d-none');
        new bootstrap.Modal(document.getElementById('importModal')).show();
    };

    document.getElementById('importModal').addEventListener('hidden.bs.modal', function () {
        if (importing) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('libraryModal')).show();
        }
    });

    window.submitImport = function () {
        if (!importing) return;
        const category = document.getElementById('importCategory').value.trim();
        if (!category) {
            document.getElementById('importError').textContent = 'Please enter a category.';
            document.getElementById('importError').classList.remove('d-none');
            return;
        }

        const btn = document.getElementById('importConfirmBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

        fetch(IMPORT_URL, {
            method: 'POST', credentials: 'same-origin',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json' },
            body: JSON.stringify({
                public_id: importing.public_id,
                url:       importing.url,
                filename:  importing.filename,
                category,
                title:    document.getElementById('importTitle').value.trim() || null,
            })
        })
        .then(r => r.ok ? r.json() : r.json().then(e => Promise.reject(e)))
        .then(data => {
            const publicId = importing.public_id;
            importing = null;
            bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
            toast(data.already_exists ? '⚠ Already in gallery.' : '✓ Added to gallery!', !data.already_exists);
            if (!data.already_exists) markCardAsInGallery(publicId);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('libraryModal')).show();
        })
        .catch(err => {
            const msg = err?.message || (err?.errors ? Object.values(err.errors).flat().join(' ') : 'Failed to save.');
            document.getElementById('importError').textContent = msg;
            document.getElementById('importError').classList.remove('d-none');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Add to Gallery';
        });
    };

    function markCardAsInGallery(publicId) {
        document.getElementById('cloudinaryGrid').querySelectorAll('.cl-card').forEach(card => {
            if (card.getAttribute('title') === publicId) {
                card.classList.add('in-gallery');
                card.removeAttribute('onclick');
                card.querySelector('img').style.opacity = '0.7';
                card.querySelector('.cl-badge').style.background = '#28a745';
                card.querySelector('.cl-badge').innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>In Gallery';
                card.style.cursor = 'default';
            }
        });
    }

    // ── Gallery select (bulk delete) ──────────────────────────────────────────
    window.galleryToggleSelect = function (card, id) {
        const idx = selected.indexOf(id);
        if (idx >= 0) {
            selected.splice(idx, 1);
            card.classList.remove('is-selected');
            card.querySelector('.gallery-check').style.display = 'none';
        } else {
            selected.push(id);
            card.classList.add('is-selected');
            card.querySelector('.gallery-check').style.display = 'block';
        }
        const count = document.getElementById('selectedCount');
        const btn   = document.getElementById('btnBulkDelete');
        if (selected.length > 0) {
            count.textContent = selected.length + ' selected';
            count.classList.remove('d-none');
            btn.classList.remove('d-none');
        } else {
            count.classList.add('d-none');
            btn.classList.add('d-none');
        }
    };

    window.galleryBulkDelete = function () {
        if (!confirm('Remove ' + selected.length + ' item(s) from gallery?')) return;
        const container = document.getElementById('bulkIds');
        container.innerHTML = '';
        selected.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
            container.appendChild(inp);
        });
        document.getElementById('bulkDeleteForm').submit();
    };

    window.galleryFilter = function () {
        const url = new URL(window.location.href);
        url.searchParams.set('category', document.getElementById('categoryFilter').value);
        url.searchParams.set('search',   document.getElementById('gallerySearch').value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    };

})();
</script>

<script>
function galleryEdit(id, imageUrl, title, description, category, sortOrder, isActive) {
    document.getElementById('galleryEditForm').action = '/admin/gallery/' + id;
    const img = document.getElementById('editPreviewImg');
    if (imageUrl) {
        img.src = imageUrl;
        img.style.display = 'block';
        document.getElementById('editPreviewEmpty').style.display = 'none';
    } else {
        img.style.display = 'none';
        document.getElementById('editPreviewEmpty').style.display = 'flex';
    }
    document.getElementById('editTitle').value       = title       || '';
    document.getElementById('editDescription').value = description || '';
    document.getElementById('editCategory').value    = category    || '';
    document.getElementById('editIsActive').checked  = isActive == 1;
    new bootstrap.Modal(document.getElementById('galleryEditModal')).show();
}
</script>

@endsection
