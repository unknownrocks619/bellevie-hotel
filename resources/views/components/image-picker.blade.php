@props([
    'name'        => 'image_id',
    'value'       => null,
    'multiple'    => false,
    'label'       => 'Image',
    'type'        => 'featured',
    'accept'      => 'image',
    'folder'      => null,
    'placeholder' => null,
])

@php
    use App\Models\Image;
    use Illuminate\Support\Str;

    // Normalise $value into an array of Image objects for display
    // Note: use Support\Collection (base class) so both Eloquent and plain collections match.
    // pluck() on an Eloquent Collection returns a Support\Collection, not Eloquent\Collection.
    $existing = [];
    if ($value) {
        if ($value instanceof \Illuminate\Support\Collection) {
            $existing = $value->filter()->values()->all();
        } elseif ($value instanceof Image) {
            $existing = [$value];
        } elseif (is_numeric($value)) {
            $img = Image::find($value);
            if ($img) $existing = [$img];
        } elseif (is_string($value) && str_starts_with($value, 'http')) {
            $fake = new Image(['url' => $value, 'original_filename' => basename($value)]);
            $existing = [$fake];
        }
    }

    $componentId  = 'imgpicker_' . Str::random(8);
    $cloudName    = config('cloudinary.cloud_name') ?: Str::after(config('cloudinary.url', ''), '@');
    $uploadPreset = config('cloudinary.upload_preset', '');
    $uploadFolder = $folder ?: config('cloudinary.upload_folder', 'bellevie_hotel');
    $inputName    = $multiple ? $name . '[]' : $name;
@endphp

{{-- ── Per-instance wrapper — config stored in data-* attributes ── --}}
<div class="image-picker-wrap"
     id="{{ $componentId }}"
     data-multiple="{{ $multiple ? '1' : '0' }}"
     data-cloud="{{ $cloudName }}"
     data-preset="{{ $uploadPreset }}"
     data-folder="{{ $uploadFolder }}"
     data-accept="{{ $accept }}"
     data-input-name="{{ $inputName }}">

    @if($label)
    <label class="form-label">{!! $label !!}</label>
    @endif

    {{-- Preview thumbnails --}}
    <div class="ip-preview-grid d-flex flex-wrap gap-2 mb-2" id="{{ $componentId }}_previews">

        @foreach($existing as $img)
        <div class="ip-thumb position-relative" data-id="{{ $img->id }}" data-url="{{ $img->url }}">
            <img src="{{ $img->url_thumb ?? $img->url }}" alt="{{ $img->original_filename }}"
                 style="width:100px;height:100px;object-fit:cover;border-radius:6px;border:2px solid #C9A227;">
            <button type="button" class="ip-remove btn btn-sm btn-danger p-0"
                    style="position:absolute;top:-6px;right:-6px;width:22px;height:22px;border-radius:50%;line-height:1;font-size:12px;"
                    onclick="ipRemoveThumb(this)" title="Remove">×</button>
            <input type="hidden" name="{{ $inputName }}" value="{{ $img->id }}">
        </div>
        @endforeach

        @if(empty($existing))
        <div class="ip-placeholder text-muted small d-flex align-items-center justify-content-center"
             style="width:100px;height:100px;border:2px dashed #dee2e6;border-radius:6px;text-align:center;padding:8px;">
            {{ $placeholder ?? 'No image selected' }}
        </div>
        @endif

    </div>

    {{-- Action buttons --}}
    <div class="d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-sm btn-outline-primary"
                onclick="ipOpenWidget('{{ $componentId }}')">
            <i class="bi bi-cloud-upload me-1"></i>Upload New
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary"
                data-bs-toggle="modal" data-bs-target="#ipLibraryModal"
                onclick="ipOpenLibrary('{{ $componentId }}')">
            <i class="bi bi-images me-1"></i>Media Library
        </button>
    </div>

</div>

{{-- ── Modal + shared JS — pushed to @stack('modals') in layout, always at body level ── --}}
@pushOnce('modals')

<div class="modal fade" id="ipLibraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-images me-2"></i>Media Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="ipLibrarySearch" class="form-control"
                           placeholder="Search by filename…" oninput="ipLibraryLoad(1)">
                </div>
                <div id="ipLibraryGrid" class="row g-2">
                    <div class="col-12 text-center py-4 text-muted">Loading…</div>
                </div>
                <div id="ipLibraryPager" class="d-flex justify-content-center gap-2 mt-3"></div>
            </div>
            <div class="modal-footer">
                <small class="text-muted me-auto" id="ipLibrarySelected">0 selected</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" style="background:#C9A227;border:none;"
                        onclick="ipLibraryConfirm()">Insert Selected</button>
            </div>
        </div>
    </div>
</div>

<script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>

<script>
(function () {
    window._ipActive     = null;
    window._ipSelections = [];

    // ── Helpers to read config from wrapper data-* attributes ─────────────────

    function cfg(componentId) {
        const el = document.getElementById(componentId);
        return el ? {
            multiple:   el.dataset.multiple === '1',
            cloud:      el.dataset.cloud,
            preset:     el.dataset.preset,
            folder:     el.dataset.folder,
            accept:     el.dataset.accept,
            inputName:  el.dataset.inputName,
        } : {};
    }

    // ── Upload via Cloudinary widget ───────────────────────────────────────────

    window.ipOpenWidget = function (componentId) {
        window._ipActive = componentId;
        const c = cfg(componentId);

        if (!c.cloud || !c.preset) {
            alert('Cloudinary is not configured.\nSet CLOUDINARY_CLOUD_NAME and CLOUDINARY_UPLOAD_PRESET in your .env file.');
            return;
        }

        window.cloudinary.openUploadWidget({
            cloud_name:    c.cloud,
            upload_preset: c.preset,
            folder:        c.folder,
            resource_type: c.accept || 'image',
            multiple:      c.multiple,
            cropping:      false,
            sources:       ['local', 'url', 'camera'],
            styles:        { palette: { action: '#C9A227', link: '#C9A227' } }
        }, function (error, result) {
            if (error) { console.error('Cloudinary widget error:', error); return; }
            if (result && result.event === 'success') {
                ipSaveAndInsert(componentId, result.info);
            }
        }).open();
    };

    window.ipSaveAndInsert = function (componentId, info) {
        const token = document.querySelector('meta[name="csrf-token"]');
        fetch('/admin/images/save', {
            method:       'POST',
            credentials:  'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token ? token.content : '',
                'Accept':       'application/json'
            },
            body: JSON.stringify(info)
        })
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status + ' — are you logged in?');
            return r.json();
        })
        .then(function (img) {
            if (!img || !img.id) { console.error('Image save: no id returned', img); return; }
            ipInsertImage(componentId, img);
        })
        .catch(function (e) {
            console.error('Image save error:', e);
            alert('Could not save image: ' + e.message);
        });
    };

    // ── Media Library ─────────────────────────────────────────────────────────

    window.ipOpenLibrary = function (componentId) {
        window._ipActive     = componentId;
        window._ipSelections = [];
        document.getElementById('ipLibrarySelected').textContent = '0 selected';
        ipLibraryLoad(1);
    };

    window.ipLibraryLoad = function (page) {
        const search = (document.getElementById('ipLibrarySearch') || {}).value || '';
        const grid   = document.getElementById('ipLibraryGrid');
        grid.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border spinner-border-sm"></div></div>';

        fetch('/admin/images?page=' + page + '&search=' + encodeURIComponent(search), {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        })
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status + (r.status === 401 || r.status === 302 ? ' — session expired, please refresh' : ''));
            return r.json();
        })
        .then(function (data) {
            if (!data.data || !data.data.length) {
                grid.innerHTML = '<div class="col-12 text-center py-4 text-muted">No images uploaded yet.</div>';
                document.getElementById('ipLibraryPager').innerHTML = '';
                return;
            }

            grid.innerHTML = data.data.map(function (img) {
                return '<div class="col-6 col-md-3 col-lg-2">'
                    + '<div class="ip-lib-item position-relative" '
                    + '     data-id="' + img.id + '" data-url="' + img.url + '" '
                    + '     data-thumb="' + (img.url_thumb || img.url) + '" '
                    + '     data-filename="' + img.original_filename + '" '
                    + '     onclick="ipLibraryToggle(this)" '
                    + '     style="cursor:pointer;border:2px solid transparent;border-radius:6px;overflow:hidden;">'
                    + '  <img src="' + (img.url_thumb || img.url) + '" alt="' + img.original_filename + '" '
                    + '       style="width:100%;aspect-ratio:1;object-fit:cover;display:block;">'
                    + '  <div style="position:absolute;inset:0;background:rgba(201,162,39,0.35);display:none;" class="ip-sel-overlay">'
                    + '    <i class="bi bi-check-circle-fill" style="position:absolute;top:6px;right:6px;color:#fff;font-size:1.25rem;"></i>'
                    + '  </div>'
                    + '  <small class="d-block text-truncate px-1 py-1" style="font-size:0.7rem;">' + img.original_filename + '</small>'
                    + '</div></div>';
            }).join('');

            // Re-mark already selected
            window._ipSelections.forEach(function (s) {
                var el = grid.querySelector('[data-id="' + s.id + '"]');
                if (el) ipMarkSelected(el, true);
            });

            // Pagination
            var pager  = document.getElementById('ipLibraryPager');
            var pages  = data.last_page;
            var current = data.current_page;
            if (pages <= 1) { pager.innerHTML = ''; return; }
            var btns = '';
            for (var p = 1; p <= pages; p++) {
                btns += '<button type="button" class="btn btn-sm '
                     + (p === current ? 'btn-primary' : 'btn-outline-secondary')
                     + '" onclick="ipLibraryLoad(' + p + ')">' + p + '</button>';
            }
            pager.innerHTML = btns;
        })
        .catch(function (e) {
            grid.innerHTML = '<div class="col-12 text-center py-4 text-danger">Failed to load images: ' + e.message + '</div>';
        });
    };

    window.ipLibraryToggle = function (el) {
        var wrap     = document.getElementById(window._ipActive);
        var multiple = wrap && wrap.dataset.multiple === '1';
        var id       = parseInt(el.dataset.id);
        var idx      = window._ipSelections.findIndex(function (s) { return s.id === id; });

        if (idx >= 0) {
            window._ipSelections.splice(idx, 1);
            ipMarkSelected(el, false);
        } else {
            if (!multiple) {
                document.querySelectorAll('.ip-lib-item').forEach(function (e) { ipMarkSelected(e, false); });
                window._ipSelections = [];
            }
            window._ipSelections.push({ id: id, url: el.dataset.url, thumb: el.dataset.thumb, filename: el.dataset.filename });
            ipMarkSelected(el, true);
        }
        document.getElementById('ipLibrarySelected').textContent = window._ipSelections.length + ' selected';
    };

    window.ipMarkSelected = function (el, on) {
        el.style.borderColor = on ? '#C9A227' : 'transparent';
        el.querySelector('.ip-sel-overlay').style.display = on ? 'block' : 'none';
    };

    window.ipLibraryConfirm = function () {
        var componentId = window._ipActive;
        window._ipSelections.forEach(function (s) { ipInsertImage(componentId, s); });
        window._ipSelections = [];
        var modal = bootstrap.Modal.getInstance(document.getElementById('ipLibraryModal'));
        if (modal) modal.hide();
    };

    // ── Insert a thumbnail card into a picker ──────────────────────────────────

    window.ipInsertImage = function (componentId, img) {
        var wrap      = document.getElementById(componentId);
        if (!wrap) return;
        var multiple  = wrap.dataset.multiple === '1';
        var inputName = wrap.dataset.inputName || componentId;
        var grid      = document.getElementById(componentId + '_previews');

        // Remove placeholder
        grid.querySelectorAll('.ip-placeholder').forEach(function (e) { e.remove(); });

        // Single-select: remove previous thumb
        if (!multiple) {
            grid.querySelectorAll('.ip-thumb').forEach(function (e) { e.remove(); });
        }

        var div = document.createElement('div');
        div.className          = 'ip-thumb position-relative';
        div.dataset.id         = img.id;
        div.dataset.url        = img.url;
        div.innerHTML = '<img src="' + (img.thumb || img.url) + '" alt="' + (img.filename || img.original_filename || '') + '" '
            + 'style="width:100px;height:100px;object-fit:cover;border-radius:6px;border:2px solid #C9A227;">'
            + '<button type="button" class="ip-remove btn btn-sm btn-danger p-0" '
            + '        style="position:absolute;top:-6px;right:-6px;width:22px;height:22px;border-radius:50%;line-height:1;font-size:12px;" '
            + '        onclick="ipRemoveThumb(this)" title="Remove">×</button>'
            + '<input type="hidden" name="' + inputName + '" value="' + img.id + '">';
        grid.appendChild(div);
    };

    // ── Remove thumb ───────────────────────────────────────────────────────────

    window.ipRemoveThumb = function (btn) {
        var thumb       = btn.closest('.ip-thumb');
        var grid        = thumb.parentElement;
        thumb.remove();

        if (!grid.querySelector('.ip-thumb')) {
            var ph = document.createElement('div');
            ph.className = 'ip-placeholder text-muted small d-flex align-items-center justify-content-center';
            ph.style.cssText = 'width:100px;height:100px;border:2px dashed #dee2e6;border-radius:6px;text-align:center;padding:8px;';
            ph.textContent = 'No image selected';
            grid.appendChild(ph);
        }
    };

})();
</script>

@endPushOnce
