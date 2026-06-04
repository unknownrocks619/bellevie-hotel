{{--
    SEO Tab Partial
    Variables expected:
      $seo    — SysSeo model instance or null
--}}
@php
    use App\Models\Image;
    // Resolve current SEO OG image as an Image model (if stored as image_id) or a legacy URL
    $seoImageValue = null;
    if (!empty($seo->feature_image_seo)) {
        if (is_numeric($seo->feature_image_seo)) {
            $seoImageValue = Image::find($seo->feature_image_seo);
        } else {
            // Legacy: plain URL — pass as-is so the picker can show it
            $seoImageValue = $seo->feature_image_seo;
        }
    }
@endphp

<div class="row g-4">

    {{-- Left column: title + description + tags --}}
    <div class="col-lg-8">

        <div class="mb-4">
            <label class="form-label fw-semibold">
                <i class="bi bi-type-h1 me-1" style="color:#C9A227;"></i>SEO Title
            </label>
            <input type="text" name="title_seo" class="form-control @error('title_seo') is-invalid @enderror"
                value="{{ old('title_seo', $seo->title_seo ?? '') }}" placeholder="Leave blank to use the page title"
                maxlength="70">
            @error('title_seo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">Appears in browser tab and search results. Ideal: 50–60 chars.</small>
                <small id="seoTitleCount" class="text-muted">0 / 70</small>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">
                <i class="bi bi-card-text me-1" style="color:#C9A227;"></i>Meta Description
            </label>
            <textarea name="description_seo" class="form-control @error('description_seo') is-invalid @enderror" rows="3"
                placeholder="Brief description shown in search results…" maxlength="160">{{ old('description_seo', $seo->description_seo ?? '') }}</textarea>
            @error('description_seo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">Displayed under the title in search results. Ideal: 120–160 chars.</small>
                <small id="seoDescCount" class="text-muted">0 / 160</small>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">
                <i class="bi bi-tags me-1" style="color:#C9A227;"></i>Meta Keywords / Tags
            </label>
            <textarea name="tags_seo" class="form-control @error('tags_seo') is-invalid @enderror" rows="2"
                placeholder="luxury hotel, boutique, spa, fine dining…">{{ old('tags_seo', $seo->tags_seo ?? '') }}</textarea>
            @error('tags_seo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Comma-separated keywords. Used by some search engines and social cards.</small>
        </div>

    </div>

    {{-- Right column: OG image picker + SERP preview --}}
    <div class="col-lg-4">

        <div class="mb-4">
            <x-image-picker
                name="seo_image_id"
                :value="$seoImageValue"
                label='<i class="bi bi-image me-1" style="color:#C9A227;"></i> Social / OG Image'
                type="seo"
                folder="bellevie_hotel/seo"
                placeholder="No OG image"
            />
            <small class="text-muted d-block mt-1">Recommended: 1200×630 px. Used for Facebook/Twitter sharing cards.</small>

            {{-- Remove existing SEO image (legacy or current) --}}
            @if(!empty($seo->feature_image_seo) && $seo->id_seo)
            <form action="{{ route('admin.seo.remove-image', $seo->id_seo) }}" method="POST"
                class="mt-2" onsubmit="return confirm('Remove the current SEO image?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>Remove current SEO image
                </button>
            </form>
            @endif
        </div>

        {{-- SERP Preview card --}}
        <div class="card border-0" style="background:#f8f9fa; border-radius:12px;">
            <div class="card-body p-3">
                <p class="mb-2 small fw-semibold text-muted"><i class="bi bi-search me-1"></i>Search Preview</p>
                <div style="font-family:arial,sans-serif; max-width:400px;">
                    <div style="color:#1a0dab; font-size:1rem; font-weight:400; line-height:1.3;" id="serpTitle">
                        {{ $seo->title_seo ?? '—' }}
                    </div>
                    <div style="color:#006621; font-size:0.78rem;">{{ url('/') }}</div>
                    <div style="color:#545454; font-size:0.83rem; line-height:1.4; margin-top:3px;" id="serpDesc">
                        {{ $seo->description_seo ?? '—' }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Character counters + SERP live preview
    (function() {
        function counter(inputName, countId, max) {
            const el = document.querySelector('[name="' + inputName + '"]');
            const cnt = document.getElementById(countId);
            if (!el || !cnt) return;
            const update = () => {
                const len = el.value.length;
                cnt.textContent = len + ' / ' + max;
                cnt.style.color = len > max * 0.9 ? '#e03131' : '#868e96';
            };
            el.addEventListener('input', update);
            update();
        }
        counter('title_seo', 'seoTitleCount', 70);
        counter('description_seo', 'seoDescCount', 160);

        const titleIn = document.querySelector('[name="title_seo"]');
        const descIn  = document.querySelector('[name="description_seo"]');
        const serpTitle = document.getElementById('serpTitle');
        const serpDesc  = document.getElementById('serpDesc');
        if (titleIn && serpTitle) titleIn.addEventListener('input', () => serpTitle.textContent = titleIn.value || '—');
        if (descIn && serpDesc)   descIn.addEventListener('input',  () => serpDesc.textContent  = descIn.value  || '—');
    })();
</script>
