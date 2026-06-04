@php
    $bgType = $config['bgType'] ?? 'image';
    $bgImageUrl = $config['bgImageUrl'] ?? '';
    $bgVideoType = $config['bgVideoType'] ?? 'youtube';
    $bgVideoUrl = $config['bgVideoUrl'] ?? '';
    $overlay = $config['overlay'] ?? 0.5;
    $parallaxSpeed = (float) ($config['parallaxSpeed'] ?? 0.4);
    $title = $config['title'] ?? '';
    $description = $config['description'] ?? '';
    $textPosition = $config['textPosition'] ?? 'center';
    $buttons = $config['buttons'] ?? [];
    $blockId = 'px_' . uniqid();
    $cloudName = config('cloudinary.cloud_name');

    // ── Resolve heights (same preset map as hero-slider) ─────────────────────
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
    $cssToPx = function (string $css, int $vh): int {
        if (str_ends_with($css, 'vh')) {
            return (int) (((int) $css / 100) * $vh);
        }
        return max(100, (int) $css);
    };

    $heights = $config['heights'] ?? [];
    $hPhone = $resolveH($heights['phone'] ?? ['preset' => 'standard']);
    $hTablet = $resolveH($heights['tablet'] ?? ['preset' => 'tall']);
    $hLaptop = $resolveH($heights['laptop'] ?? ['preset' => 'tall']);
    $hDesktop = $resolveH($heights['desktop'] ?? ['preset' => 'extratall']);

    // ── Cloudinary-optimised background image per breakpoint ─────────────────
    $bpWidths = ['phone' => 640, 'tablet' => 1024, 'laptop' => 1440, 'desktop' => 1920];
    $bpVH = ['phone' => 812, 'tablet' => 1024, 'laptop' => 900, 'desktop' => 1080];

    $extractPid = function (string $url): ?string {
        if (!str_contains($url, 'res.cloudinary.com')) {
            return null;
        }
        if (!preg_match('#/upload/(?:v\d+/)?(.+)$#i', $url, $m)) {
            return null;
        }
        return preg_replace('/\.[a-z]{3,4}$/i', '', $m[1]);
    };

    $cldUrl = function (string $pid, int $w, int $h) use ($cloudName): string {
        if (!$cloudName) {
            return '';
        }
        return "https://res.cloudinary.com/{$cloudName}/image/upload/c_fill,w_{$w},h_{$h},g_auto,q_auto,f_auto/{$pid}";
    };

    $bgPid = $bgType === 'image' ? $extractPid($bgImageUrl) : null;

    $bgImages = [];
    foreach (['phone', 'tablet', 'laptop', 'desktop'] as $bp) {
        $hCss = match ($bp) {
            'phone' => $hPhone,
            'tablet' => $hTablet,
            'laptop' => $hLaptop,
            'desktop' => $hDesktop,
        };
        $hPx = $cssToPx($hCss, $bpVH[$bp]);
        $bgImages[$bp] = $bgPid && $cloudName ? $cldUrl($bgPid, $bpWidths[$bp], $hPx) : $bgImageUrl;
    }

    // ── YouTube embed helper ──────────────────────────────────────────────────
    $ytEmbed = function (string $url): string {
        preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $url, $m);
        $vid = $m[1] ?? '';
        if (!$vid) {
            return '<p class="text-white">Invalid YouTube URL.</p>';
        }
        return "<div style='position:relative;padding-bottom:56.25%;height:0;overflow:hidden;'>
            <iframe src='https://www.youtube.com/embed/{$vid}?autoplay=0&rel=0' frameborder='0'
                allow='autoplay;fullscreen' allowfullscreen
                style='position:absolute;top:0;left:0;width:100%;height:100%;'></iframe>
        </div>";
    };

    // ── Filter visible buttons ────────────────────────────────────────────────
    $visibleButtons = collect($buttons)
        ->filter(fn($b) => !empty(trim($b['text'] ?? '')) && (!empty($b['link']) || ($b['action'] ?? '') === 'modal'))
        ->values();

    $textAlign = match ($textPosition) {
        'left' => 'left',
        'right' => 'right',
        default => 'center',
    };
    $flexAlign = match ($textPosition) {
        'left' => 'flex-start',
        'right' => 'flex-end',
        default => 'center',
    };

    $isVideo = $bgType === 'video';
@endphp

{{-- ── Responsive CSS ────────────────────────────────────────────────── --}}
<style>
    #{{ $blockId }} {
        min-height: {{ $hDesktop }};
    }

    #{{ $blockId }} .px-inner {
        min-height: inherit;
    }

    @media (max-width:1199px) {
        #{{ $blockId }} {
            min-height: {{ $hLaptop }};
        }
    }

    @media (max-width:991px) {
        #{{ $blockId }} {
            min-height: {{ $hTablet }};
        }
    }

    @media (max-width:575px) {
        #{{ $blockId }} {
            min-height: {{ $hPhone }};
        }
    }

    @if ($bgType === 'image' && $bgImageUrl)
        /* Background images — browser loads only the matching breakpoint */
        #{{ $blockId }} .px-bg {
            background-image: url('{{ $bgImages['desktop'] }}');
        }

        @media (max-width:1199px) {
            #{{ $blockId }} .px-bg {
                background-image: url('{{ $bgImages['laptop'] }}');
            }
        }

        @media (max-width:991px) {
            #{{ $blockId }} .px-bg {
                background-image: url('{{ $bgImages['tablet'] }}');
            }
        }

        @media (max-width:575px) {
            #{{ $blockId }} .px-bg {
                background-image: url('{{ $bgImages['phone'] }}');
            }
        }

        /* Parallax on desktop; scroll on mobile (fixed doesn't work on iOS) */
        @media (min-width:992px) {
            #{{ $blockId }} .px-bg {
                background-attachment: fixed;
                background-position: center center;
            }
        }
    @endif
</style>

<section id="{{ $blockId }}" data-parallax-speed="{{ $parallaxSpeed }}"
    style="position:relative;overflow:hidden;background:#0D1B2A;">

    @if ($isVideo)
        {{-- ── Video Background ─────────────────────────────────────────── --}}
        @if ($bgVideoType === 'youtube')
            @php
                preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $bgVideoUrl, $ytm);
                $ytId = $ytm[1] ?? '';
            @endphp
            @if ($ytId)
                <div class="px-bg" style="position:absolute;inset:0;overflow:hidden;pointer-events:none;">
                    <iframe
                        src="https://www.youtube.com/embed/{{ $ytId }}?autoplay=1&mute=1&loop=1&playlist={{ $ytId }}&controls=0&showinfo=0&rel=0&playsinline=1"
                        frameborder="0" allow="autoplay" allowfullscreen
                        style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                   width:177.78vh;min-width:100%;height:56.25vw;min-height:100%;"></iframe>
                </div>
            @endif
        @else
            {{-- Cloudinary video --}}
            <div class="px-bg" style="position:absolute;inset:0;overflow:hidden;">
                <video autoplay muted loop playsinline
                    style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                      min-width:100%;min-height:100%;object-fit:cover;">
                    <source src="{{ $bgVideoUrl }}" type="video/mp4">
                </video>
            </div>
        @endif
    @else
        {{-- ── Image Background (with JS parallax on desktop) ──────────── --}}
        <div class="px-bg"
            style="position:absolute;inset:-20%;
                background-size:cover;
                background-position:center center;
                background-repeat:no-repeat;">
        </div>
    @endif

    {{-- Overlay --}}
    <div style="position:absolute;inset:0;background:rgba(0,0,0,{{ $overlay }});"></div>

    {{-- Content --}}
    <div class="px-inner"
        style="position:relative;z-index:2;
                display:flex;align-items:center;
                justify-content:{{ $flexAlign }};
                padding:80px 48px;">

        <div style="max-width:700px;text-align:{{ $textAlign }};color:#fff;">

            @if ($title)
                <h2
                    style="font-family:Georgia,serif;
                       font-size:clamp(2rem,4.5vw,3.2rem);
                       font-weight:700;margin-bottom:20px;line-height:1.2;
                       text-shadow:0 3px 14px rgba(0,0,0,.5);">
                    {{ $title }}
                </h2>
            @endif

            @if ($description)
                <p
                    style="font-size:clamp(1rem,2vw,1.2rem);
                      opacity:.9;margin-bottom:32px;
                      line-height:1.8;
                      text-shadow:0 1px 8px rgba(0,0,0,.4);">
                    {{ $description }}
                </p>
            @endif

            {{-- Buttons --}}
            @if ($visibleButtons->isNotEmpty())
                <div
                    style="display:flex;flex-wrap:wrap;gap:14px;justify-content:{{ $textPosition === 'right' ? 'flex-end' : ($textPosition === 'left' ? 'flex-start' : 'center') }};">
                    @foreach ($visibleButtons as $bi => $btn)
                        @php
                            $isModal = ($btn['action'] ?? 'link') === 'modal';
                            $modalId = $blockId . '_modal_' . $bi;
                            $isPrimary = $bi === 0;
                        @endphp

                        @if ($isModal)
                            <button type="button" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}"
                                style="display:inline-block;
                           padding:14px 36px;border-radius:4px;
                           font-size:0.95rem;font-weight:600;letter-spacing:.03em;
                           cursor:pointer;transition:all .2s;
                           {{ $isPrimary ? 'background:#C9A227;color:#fff;border:2px solid #C9A227;' : 'background:transparent;color:#fff;border:2px solid rgba(255,255,255,.7);' }}">
                                {{ $btn['text'] }}
                            </button>
                        @else
                            <a href="{{ $btn['link'] ?? '#' }}"
                                style="display:inline-block;
                          padding:14px 36px;border-radius:4px;
                          font-size:0.95rem;font-weight:600;letter-spacing:.03em;
                          text-decoration:none;transition:all .2s;
                          {{ $isPrimary ? 'background:#C9A227;color:#fff;border:2px solid #C9A227;' : 'background:transparent;color:#fff;border:2px solid rgba(255,255,255,.7);' }}">
                                {{ $btn['text'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif

        </div>
    </div>

</section>

{{-- ── Bootstrap Modals (one per modal-action button) ─────────────────── --}}
@foreach ($visibleButtons as $bi => $btn)
    @if (($btn['action'] ?? 'link') === 'modal')
        @php
            $modalId = $blockId . '_modal_' . $bi;
            $isVideo2 = ($btn['modalType'] ?? 'text') === 'video';
            $modalTitle = $btn['modalTitle'] ?? ($btn['text'] ?? '');
            $modalDesc = $btn['modalDescription'] ?? '';
            $mVidType = $btn['modalVideoType'] ?? 'youtube';
            $mVidUrl = $btn['modalVideoUrl'] ?? '';
        @endphp
        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-label="{{ $modalTitle }}"
            aria-hidden="true">
            <div class="modal-dialog {{ $isVideo2 ? 'modal-xl modal-dialog-centered' : 'modal-dialog-centered' }}">
                <div class="modal-content" style="background:#0D1B2A;color:#fff;border:1px solid rgba(201,162,39,.3);">
                    <div class="modal-header" style="border-bottom:1px solid rgba(201,162,39,.2);">
                        @if ($modalTitle)
                            <h5 class="modal-title" style="font-family:Georgia,serif;color:#fff;">{{ $modalTitle }}
                            </h5>
                        @endif
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding:{{ $isVideo2 ? '0' : '28px 32px' }};">
                        @if ($isVideo2)
                            @if ($mVidType === 'youtube')
                                {!! $ytEmbed($mVidUrl) !!}
                            @else
                                {{-- Cloudinary video --}}
                                <video controls autoplay
                                    style="width:100%;max-height:70vh;display:block;background:#000;">
                                    <source src="{{ $mVidUrl }}" type="video/mp4">
                                    Your browser does not support video playback.
                                </video>
                            @endif
                        @else
                            @if ($modalTitle)
                                <h4 style="font-family:Georgia,serif;color:#C9A227;margin-bottom:16px;">
                                    {{ $modalTitle }}</h4>
                            @endif
                            @if ($modalDesc)
                                <div style="color:rgba(255,255,255,.85);line-height:1.8;font-size:1rem;">
                                    {!! nl2br(e($modalDesc)) !!}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@once
    <script>
        (function() {
            // JS parallax for image-background parallax sections on desktop
            const pxSections = document.querySelectorAll('.bellevie-px-section');

            function onScroll() {
                if (window.innerWidth < 992) return; // disable on mobile/tablet
                pxSections.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.bottom < 0 || rect.top > window.innerHeight) return;
                    const speed = parseFloat(el.dataset.parallaxSpeed || 0.4);
                    const bg = el.querySelector('.px-bg');
                    if (!bg) return;
                    const offset = (rect.top * speed * 0.5);
                    bg.style.transform = `translateY(${offset}px)`;
                });
            }

            window.addEventListener('scroll', onScroll, {
                passive: true
            });
            onScroll();
        })
        ();
    </script>
@endonce

{{-- Add section class for JS parallax (only for image bg) --}}
@if ($bgType === 'image' && $bgImageUrl && $parallaxSpeed > 0)
    <script>
        (function() {
            const el = document.getElementById('{{ $blockId }}');
            if (el) el.classList.add('bellevie-px-section');
        })();
    </script>
@endif
