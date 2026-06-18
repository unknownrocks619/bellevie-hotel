@php
    $imgPos = $config['imagePosition'] ?? 'left';
    $bg = $config['bgColor'] ?? '#ffffff';
    $imgUrl = $config['imageUrl'] ?? '';
    $blockId = 'ab_' . uniqid();

    $stats = [];
    for ($i = 1; $i <= 3; $i++) {
        $val = $config["stat{$i}Value"] ?? null;
        $lbl = $config["stat{$i}Label"] ?? null;
        if ($val) {
            $stats[] = ['value' => $val, 'label' => $lbl];
        }
    }

    // ── Responsive min-height ─────────────────────────────────────────────────
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
        return $presetMap[$bpCfg['preset'] ?? ''] ?? 'auto';
    };
    $heights = $config['heights'] ?? [];
    $hasH = !empty($heights);
    $hPhone = $hasH ? $resolveH($heights['phone'] ?? ['preset' => 'standard']) : 'auto';
    $hTablet = $hasH ? $resolveH($heights['tablet'] ?? ['preset' => 'standard']) : 'auto';
    $hLaptop = $hasH ? $resolveH($heights['laptop'] ?? ['preset' => 'standard']) : 'auto';
    $hDesktop = $hasH ? $resolveH($heights['desktop'] ?? ['preset' => 'standard']) : 'auto';
@endphp

@if ($hasH)
    <style>
        #{{ $blockId }} {
            min-height: {{ $hDesktop }};
            display: flex;
            align-items: center;
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
    </style>
@endif

<section id="{{ $blockId }}" style="background:{{ $bg }};padding:5px 0;width:100%;">
    <div class="container">
        <div class="row align-items-center {{ $imgPos === 'right' ? 'flex-row-reverse' : '' }}">

            {{-- Image column --}}
            <div class="col-lg-5">
                @if ($imgUrl)
                    <div style="position:relative;border:3px solid #C9A227;padding:0.75rem;">
                        <img src="{{ $imgUrl }}" alt="{{ $config['title'] ?? '' }}" class="img-fluid d-block w-100"
                            style="display:block;">
                    </div>
                @else
                    <div
                        style="height:360px;background:linear-gradient(135deg,#e0d4b0,#C9A22740);
                            border:3px solid #C9A22740;display:flex;align-items:center;
                            justify-content:center;border-radius:4px;">
                        <i class="bi bi-building" style="font-size:4rem;color:#C9A22760;"></i>
                    </div>
                @endif
            </div>

            {{-- Text column --}}
            <div class="col-lg-7">
                @if (!empty($config['label']))
                    <p
                        style="color:#C9A227;font-weight:700;font-size:0.8rem;
                          letter-spacing:0.12em;text-transform:uppercase;margin-bottom:12px;">
                        {{ $config['label'] }}
                    </p>
                @endif

                @if (!empty($config['title']))
                    <h2 class="mb-4" style="font-family:'Playfair Display',Georgia,serif;font-size:2.2rem;">
                        {{ $config['title'] }}
                    </h2>
                @endif

                @if (!empty($config['content']))
                    <div class="mb-4" style="color:#555;line-height:1.9;">
                        {!! $config['content'] !!}
                    </div>
                @endif

                @if (!empty($stats))
                    <div class="row g-3 mb-4">
                        @foreach ($stats as $stat)
                            <div class="col-4">
                                <div style="border-left:3px solid #C9A227;padding-left:14px;">
                                    <div
                                        style="font-size:1.8rem;font-weight:700;color:#C9A227;
                                        font-family:'Playfair Display',Georgia,serif;">
                                        {{ $stat['value'] }}
                                    </div>
                                    <div
                                        style="font-size:0.8rem;color:#888;
                                        text-transform:uppercase;letter-spacing:.05em;">
                                        {{ $stat['label'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (!empty($config['ctaText']))
                    <a href="{{ $config['ctaUrl'] ?? '#' }}" class="btn px-4 py-2"
                        style="background:#C9A227;color:#fff;border:none;border-radius:4px;">
                        {{ $config['ctaText'] }}
                    </a>
                @endif
            </div>

        </div>
    </div>
</section>
