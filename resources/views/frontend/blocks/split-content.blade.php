@php
    $imagePosition   = $config['imagePosition']   ?? 'right';
    $eyebrow         = $config['eyebrow']          ?? '';
    $title           = $config['title']            ?? '';
    $content         = $config['content']          ?? '';
    $subheading      = $config['subheading']       ?? '';
    $subContent      = $config['subContent']       ?? '';
    $contactIcon     = $config['contactIcon']      ?? 'bi-telephone';
    $contactLabel    = $config['contactLabel']     ?? '';
    $contactValue    = $config['contactValue']     ?? '';
    $contactLink     = $config['contactLink']      ?? '';
    $image1Url       = $config['image1Url']        ?? '';
    $image2Url       = $config['image2Url']        ?? '';
    $bgColor         = $config['bgColor']          ?? '#ffffff';
    $verticalPadding = max(20, (int)($config['verticalPadding'] ?? 80));
    $cloudName       = config('cloudinary.cloud_name');

    $imageLeft  = $imagePosition === 'left';
    $hasContact = !empty($contactValue);
    $hasTwoImgs = !empty($image1Url) && !empty($image2Url);
    $hasOneImg  = !empty($image1Url) || !empty($image2Url);
    // Normalise so image1 is always the first available
    if (empty($image1Url) && !empty($image2Url)) {
        $image1Url = $image2Url;
        $image2Url = '';
        $hasTwoImgs = false;
    }

    // ── Cloudinary optimisation ──────────────────────────────────────────────
    // Each image occupies roughly half the viewport width at desktop
    // When two images share the column, each is ~25vw; one image gets ~50vw.
    $extractPid = function(string $url): ?string {
        if (!str_contains($url, 'res.cloudinary.com')) return null;
        if (!preg_match('#/upload/(?:v\d+/)?(.+)$#i', $url, $m)) return null;
        return preg_replace('/\.[a-z]{3,4}$/i', '', $m[1]);
    };
    $cldUrl = function(string $pid, int $w, int $h) use ($cloudName): string {
        if (!$cloudName || !$pid) return '';
        return "https://res.cloudinary.com/{$cloudName}/image/upload/c_fill,w_{$w},h_{$h},g_auto,q_auto,f_auto/{$pid}";
    };

    $pid1 = $extractPid($image1Url);
    $pid2 = $extractPid($image2Url);

    // Target dimensions: taller when single, shorter when two images sit side by side
    [$imgW, $imgH] = $hasTwoImgs ? [700, 500] : [800, 620];

    $src1 = ($pid1 && $cloudName) ? $cldUrl($pid1, $imgW, $imgH) : $image1Url;
    $src2 = ($pid2 && $cloudName) ? $cldUrl($pid2, $imgW, $imgH) : $image2Url;
@endphp

<section style="background:{{ $bgColor }};padding:{{ $verticalPadding }}px 0;">
    <div class="container">
        <div class="row align-items-center g-5 {{ $imageLeft ? 'flex-row-reverse' : '' }}">

            {{-- ── Text column ─────────────────────────────────────────── --}}
            <div class="col-lg-6">

                @if($eyebrow)
                <p style="color:#C9A227;font-weight:700;font-size:0.78rem;
                           letter-spacing:0.14em;text-transform:uppercase;margin-bottom:14px;">
                    {{ $eyebrow }}
                </p>
                @endif

                @if($title)
                <h2 style="font-family:'Playfair Display',Georgia,serif;
                           font-size:clamp(2rem,3.5vw,3rem);
                           font-weight:700;line-height:1.2;margin-bottom:20px;color:#0D1B2A;">
                    {{ $title }}
                </h2>
                @endif

                @if($content)
                <div style="color:#555;line-height:1.9;font-size:1rem;margin-bottom:20px;">
                    {!! $content !!}
                </div>
                @endif

                @if($subheading)
                <h3 style="font-size:1.25rem;font-weight:700;color:#0D1B2A;
                           margin-bottom:12px;line-height:1.3;">
                    {{ $subheading }}:
                </h3>
                @endif

                @if($subContent)
                <div style="color:#555;line-height:1.9;font-size:1rem;margin-bottom:24px;">
                    {!! $subContent !!}
                </div>
                @endif

                @if($hasContact)
                <div style="display:flex;align-items:center;gap:16px;margin-top:8px;">
                    <div style="width:52px;height:52px;border-radius:50%;flex-shrink:0;
                                background:#C9A22715;border:2px solid #C9A22730;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="bi {{ $contactIcon }}"
                           style="font-size:1.25rem;color:#C9A227;"></i>
                    </div>
                    <div>
                        @if($contactLabel)
                        <p style="font-size:0.78rem;color:#888;margin:0 0 2px;
                                  text-transform:uppercase;letter-spacing:.06em;">
                            {{ $contactLabel }}
                        </p>
                        @endif
                        @if($contactLink)
                        <a href="{{ $contactLink }}"
                           style="font-size:1.3rem;font-weight:700;color:#C9A227;
                                  text-decoration:none;letter-spacing:.02em;">
                            {{ $contactValue }}
                        </a>
                        @else
                        <span style="font-size:1.3rem;font-weight:700;color:#C9A227;
                                     letter-spacing:.02em;">
                            {{ $contactValue }}
                        </span>
                        @endif
                    </div>
                </div>
                @endif

            </div>

            {{-- ── Image column ─────────────────────────────────────────── --}}
            @if($hasOneImg)
            <div class="col-lg-6">
                @if($hasTwoImgs)
                {{-- Two images side-by-side, different heights for visual interest --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;align-items:stretch;">
                    <div style="border-radius:8px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.12);">
                        <img src="{{ $src1 }}" alt="{{ $title }}"
                             style="width:100%;height:380px;object-fit:cover;display:block;">
                    </div>
                    <div style="border-radius:8px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.12);
                                margin-top:40px;">
                        <img src="{{ $src2 }}" alt="{{ $title }}"
                             style="width:100%;height:380px;object-fit:cover;display:block;">
                    </div>
                </div>
                @else
                {{-- Single image --}}
                <div style="border-radius:8px;overflow:hidden;box-shadow:0 6px 30px rgba(0,0,0,.14);">
                    <img src="{{ $src1 }}" alt="{{ $title }}"
                         class="img-fluid d-block w-100"
                         style="max-height:520px;object-fit:cover;">
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>
</section>
