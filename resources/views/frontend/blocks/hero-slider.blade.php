@php
    $slides = $config['slides'] ?? [];
    $animation = $config['animation'] ?? 'fade';
    $autoplay = $config['autoplay'] ?? true;
    $autoplaySpeed = (int) ($config['autoplaySpeed'] ?? 5) * 1000;
    $overlay = $config['overlay'] ?? 0.4;
    $sliderId = 'hs_' . uniqid();
    $cloudName = config('cloudinary.cloud_name');

    // ── Height preset → CSS value ─────────────────────────────────────────────
    $presetMap = [
        'compact' => '320px',
        'standard' => '520px',
        'tall' => '680px',
        'extratall' => '820px',
        'fullscreen' => '100vh',
        'almostfull' => '90vh',
        'threequarters' => '75vh',
    ];

    $resolveH = function (array $bpCfg) use ($presetMap): string {
        if (($bpCfg['preset'] ?? '') === 'custom') {
            return max(100, (int) ($bpCfg['custom'] ?? 520)) . 'px';
        }
        return $presetMap[$bpCfg['preset'] ?? ''] ?? '520px';
    };

    // CSS value → pixel integer (for Cloudinary height param)
    // We use the representative viewport height for each breakpoint
    $cssToPx = function (string $css, int $viewportH): int {
        $css = trim($css);
        if (str_ends_with($css, 'vh')) {
            return (int) (((int) $css / 100) * $viewportH);
        }
        return max(100, (int) $css);
    };

    $heights = $config['heights'] ?? [];
    $hPhone = $resolveH($heights['phone'] ?? ['preset' => 'compact']);
    $hTablet = $resolveH($heights['tablet'] ?? ['preset' => 'standard']);
    $hLaptop = $resolveH($heights['laptop'] ?? ['preset' => 'tall']);
    $hDesktop = $resolveH($heights['desktop'] ?? ['preset' => 'extratall']);

    // Fallback: support old single minHeight field
    if (empty($heights) && isset($config['minHeight'])) {
        $hPhone = $hTablet = $hLaptop = $hDesktop = $config['minHeight'] . 'px';
    }

    // ── Cloudinary image sizing per breakpoint ────────────────────────────────
    //
    // For each breakpoint we know:
    //   • max width of the viewport          → image width to request
    //   • representative viewport height     → used to calculate vh-based heights
    //
    // We use c_fill,g_auto so Cloudinary auto-crops from the best focal point
    // while filling the target area exactly — no distortion, no empty space.
    // f_auto picks the best format (WebP, AVIF) and q_auto compresses optimally.

    $breakpointWidths = [
        'phone' => 640, // retina phone, max 575px logical
        'tablet' => 1024, // retina tablet, max 991px logical
        'laptop' => 1440, // hi-dpi laptop, max 1199px logical
        'desktop' => 1920, // Full HD
    ];
    $breakpointVH = [
        'phone' => 812, // iPhone X height
        'tablet' => 1024, // iPad
        'laptop' => 900, // typical laptop
        'desktop' => 1080, // 1080p
    ];

    /**
     * Extract the Cloudinary public_id from any Cloudinary URL.
     * Handles: /upload/v123456/folder/file.jpg
     *          /upload/c_fill,w_200/folder/file.jpg
     *          /upload/folder/file.jpg
     */
    $extractPublicId = function (string $url): ?string {
        if (!str_contains($url, 'res.cloudinary.com')) {
            return null;
        }

        // Capture everything after /upload/, skipping transforms & version
        if (!preg_match('#/upload/(?:v\d+/)?(.+)$#i', $url, $m)) {
            return null;
        }

        // Strip file extension
        return preg_replace('/\.[a-z]{3,4}$/i', '', $m[1]);
    };

    /**
     * Build a fresh Cloudinary URL with the given transformation.
     * Uses c_fill + g_auto (smart AI focal-point crop) so the most
     * important part of the image is always visible.
     */
    $buildCldUrl = function (string $publicId, int $w, int $h) use ($cloudName): string {
        if (!$cloudName) {
            return '';
        }
        $t = "c_fill,w_{$w},h_{$h},g_auto,q_auto,f_auto";
        return "https://res.cloudinary.com/{$cloudName}/image/upload/{$t}/{$publicId}";
    };

    // Pre-compute per-slide, per-breakpoint image URLs
    $slides = collect($slides)->filter(fn($s) => !empty($s['imageUrl']))->values();
    if ($slides->isEmpty()) {
        return;
    }

    // Build the image URL map:  $imgUrls[$slideIndex][$breakpoint] = url
    $imgUrls = [];
    foreach ($slides as $i => $slide) {
        $pid = $extractPublicId($slide['imageUrl']);

        foreach (['phone', 'tablet', 'laptop', 'desktop'] as $bp) {
            $w = $breakpointWidths[$bp];
            $hCss = match ($bp) {
                'phone' => $hPhone,
                'tablet' => $hTablet,
                'laptop' => $hLaptop,
                'desktop' => $hDesktop,
            };
            $hPx = $cssToPx($hCss, $breakpointVH[$bp]);
            $imgUrls[$i][$bp] = $pid && $cloudName ? $buildCldUrl($pid, $w, $hPx) : $slide['imageUrl']; // fallback: original URL
        }
    }
@endphp

{{-- ── Per-slider responsive CSS ─────────────────────────────────────────────
     Heights are set here so they respond to screen size.
     Background images are also set per-breakpoint so the browser
     requests only the optimally-sized Cloudinary image for the
     current screen — never the original full-resolution file.
────────────────────────────────────────────────────────────────────────── --}}
<style>
    /* Heights */
    #{{ $sliderId }},
    #{{ $sliderId }} .bhs-track {
        min-height: {{ $hDesktop }};
    }

    #{{ $sliderId }} .bhs-slide,
    #{{ $sliderId }} .bhs-content {
        min-height: inherit;
    }

    @media (max-width: 1199px) {

        #{{ $sliderId }},
        #{{ $sliderId }} .bhs-track {
            min-height: {{ $hLaptop }};
        }
    }

    @media (max-width: 991px) {

        #{{ $sliderId }},
        #{{ $sliderId }} .bhs-track {
            min-height: {{ $hTablet }};
        }
    }

    @media (max-width: 575px) {

        #{{ $sliderId }},
        #{{ $sliderId }} .bhs-track {
            min-height: {{ $hPhone }};
        }
    }

    @foreach ($slides as $i => $slide)
        /* Slide {{ $i }} — desktop (default) */
        #{{ $sliderId }} .bhs-bg-{{ $i }} {
            background-image: url('{{ $imgUrls[$i]['desktop'] }}');
        }

        /* Slide {{ $i }} — laptop */
        @media (max-width: 1199px) {
            #{{ $sliderId }} .bhs-bg-{{ $i }} {
                background-image: url('{{ $imgUrls[$i]['laptop'] }}');
            }
        }

        /* Slide {{ $i }} — tablet */
        @media (max-width: 991px) {
            #{{ $sliderId }} .bhs-bg-{{ $i }} {
                background-image: url('{{ $imgUrls[$i]['tablet'] }}');
            }
        }

        /* Slide {{ $i }} — phone */
        @media (max-width: 575px) {
            #{{ $sliderId }} .bhs-bg-{{ $i }} {
                background-image: url('{{ $imgUrls[$i]['phone'] }}');
            }
        }
    @endforeach
</style>

<section id="{{ $sliderId }}" class="bellevie-hero-slider"
    style="position:relative;overflow:hidden;background:#0D1B2A;">

    <div class="bhs-track" style="position:relative;width:100%;">
        @foreach ($slides as $i => $slide)
            @php
                $align = match ($slide['textPosition'] ?? 'center') {
                    'left' => 'flex-start',
                    'right' => 'flex-end',
                    default => 'center',
                };
                $textAlign = $slide['textPosition'] ?? 'center';
                $hasBtn = !empty(trim($slide['buttonText'] ?? ''));
            @endphp

            <div class="bhs-slide" data-index="{{ $i }}"
                style="position:absolute;inset:0;
                    opacity:{{ $i === 0 ? '1' : '0' }};
                    transition:opacity 0.7s ease;
                    pointer-events:{{ $i === 0 ? 'auto' : 'none' }};
                    z-index:{{ $i === 0 ? 2 : 1 }};">

                {{-- Background image (responsive, Cloudinary-optimised) --}}
                <div class="bhs-bg-{{ $i }}"
                    style="position:absolute;inset:0;
                        background-size:cover;
                        background-position:center center;
                        background-repeat:no-repeat;">
                </div>

                {{-- Overlay --}}
                <div style="position:absolute;inset:0;background:rgba(0,0,0,{{ $overlay }});"></div>

                {{-- Text content --}}
                <div class="bhs-content"
                    style="position:relative;z-index:2;
                        display:flex;align-items:center;
                        justify-content:{{ $align }};
                        padding:60px 80px;">
                    <div style="max-width:640px;text-align:{{ $textAlign }};color:#fff;">

                        @if (!empty($slide['title']))
                            <h1
                                style="font-family:Georgia,serif;
                               font-size:clamp(1.8rem,4vw,3rem);
                               font-weight:700;margin-bottom:16px;line-height:1.2;
                               text-shadow:0 2px 12px rgba(0,0,0,.5);">
                                {{ $slide['title'] }}
                            </h1>
                        @endif

                        @if (!empty($slide['description']))
                            <p
                                style="font-size:clamp(1rem,2vw,1.15rem);
                              opacity:.9;margin-bottom:28px;
                              line-height:1.7;
                              text-shadow:0 1px 6px rgba(0,0,0,.4);">
                                {{ $slide['description'] }}
                            </p>
                        @endif

                        @if ($hasBtn)
                            <a href="{{ $slide['buttonLink'] ?? '#' }}"
                                style="display:inline-block;background:#C9A227;color:#fff;
                              padding:14px 36px;border-radius:4px;font-size:0.95rem;
                              font-weight:600;text-decoration:none;letter-spacing:.03em;
                              box-shadow:0 4px 16px rgba(201,162,39,.4);"
                                onmouseover="this.style.background='#b08c20';this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.background='#C9A227';this.style.transform='none'">
                                {{ $slide['buttonText'] }}
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($slides->count() > 1)
        {{-- Arrows --}}
        <button onclick="bhsMove('{{ $sliderId }}',-1)"
            style="position:absolute;top:50%;left:20px;transform:translateY(-50%);z-index:10;
                   width:48px;height:48px;border-radius:50%;
                   border:2px solid rgba(255,255,255,.5);
                   background:rgba(0,0,0,.3);color:#fff;font-size:1.4rem;cursor:pointer;
                   display:flex;align-items:center;justify-content:center;
                   transition:background .2s,border-color .2s;"
            onmouseover="this.style.background='rgba(201,162,39,.8)';this.style.borderColor='#C9A227'"
            onmouseout="this.style.background='rgba(0,0,0,.3)';this.style.borderColor='rgba(255,255,255,.5)'">‹</button>

        <button onclick="bhsMove('{{ $sliderId }}',1)"
            style="position:absolute;top:50%;right:20px;transform:translateY(-50%);z-index:10;
                   width:48px;height:48px;border-radius:50%;
                   border:2px solid rgba(255,255,255,.5);
                   background:rgba(0,0,0,.3);color:#fff;font-size:1.4rem;cursor:pointer;
                   display:flex;align-items:center;justify-content:center;
                   transition:background .2s,border-color .2s;"
            onmouseover="this.style.background='rgba(201,162,39,.8)';this.style.borderColor='#C9A227'"
            onmouseout="this.style.background='rgba(0,0,0,.3)';this.style.borderColor='rgba(255,255,255,.5)'">›</button>

        {{-- Dots --}}
        <div
            style="position:absolute;bottom:22px;left:50%;transform:translateX(-50%);
                z-index:10;display:flex;gap:8px;align-items:center;">
            @foreach ($slides as $i => $slide)
                <button onclick="bhsGoTo('{{ $sliderId }}',{{ $i }})" class="bhs-dot"
                    data-slider="{{ $sliderId }}" data-dot="{{ $i }}"
                    style="width:{{ $i === 0 ? '28px' : '10px' }};height:10px;border-radius:20px;
                       border:none;cursor:pointer;padding:0;
                       background:{{ $i === 0 ? '#C9A227' : 'rgba(255,255,255,.5)' }};
                       transition:width .3s,background .3s;"></button>
            @endforeach
        </div>
    @endif

</section>

@once
    <script>
        (function() {
            const sliders = {};

            function init(id) {
                const el = document.getElementById(id);
                if (!el) return;
                const slides = el.querySelectorAll('.bhs-slide');
                const dots = el.querySelectorAll('.bhs-dot');
                sliders[id] = {
                    el,
                    slides,
                    dots,
                    current: 0,
                    total: slides.length,
                    timer: null
                };
            }

            function goTo(id, index) {
                const s = sliders[id];
                if (!s) return;
                s.current = ((index % s.total) + s.total) % s.total;
                s.slides.forEach((slide, i) => {
                    const active = i === s.current;
                    slide.style.opacity = active ? '1' : '0';
                    slide.style.pointerEvents = active ? 'auto' : 'none';
                    slide.style.zIndex = active ? '2' : '1';
                });
                s.dots.forEach((dot, i) => {
                    const active = i === s.current;
                    dot.style.width = active ? '28px' : '10px';
                    dot.style.background = active ? '#C9A227' : 'rgba(255,255,255,.5)';
                });
            }

            window.bhsGoTo = function(id, index) {
                const s = sliders[id];
                if (s) {
                    clearTimeout(s.timer);
                    goTo(id, index);
                }
            };
            window.bhsMove = function(id, dir) {
                const s = sliders[id];
                if (s) {
                    clearTimeout(s.timer);
                    goTo(id, s.current + dir);
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.bellevie-hero-slider').forEach(el => {
                    init(el.id);
                    const speed = parseInt(el.dataset.speed || '5000');
                    const doAuto = el.dataset.autoplay !== 'false';
                    if (doAuto && sliders[el.id] && sliders[el.id].total > 1) {
                        (function tick() {
                            const s = sliders[el.id];
                            if (!s) return;
                            goTo(el.id, s.current + 1);
                            s.timer = setTimeout(tick, speed);
                        })();
                    }
                });
            });
        })
        ();
    </script>
@endonce

<script>
    (function() {
        const el = document.getElementById('{{ $sliderId }}');
        if (el) {
            el.dataset.speed = '{{ $autoplaySpeed }}';
            el.dataset.autoplay = '{{ $autoplay ? 'true' : 'false' }}';
        }
    })();
</script>
