@php
    $iframeCode = $config['iframeCode'] ?? '';
    $title = trim($config['title'] ?? '');
    $description = trim($config['description'] ?? '');
    $widthType = $config['widthType'] ?? 'full';
    $customPct = max(10, min(100, (int) ($config['customWidthPct'] ?? 100)));
    $bgColor = $config['bgColor'] ?? '#ffffff';
    $padding = max(0, (int) ($config['padding'] ?? 40));
    $embedId = 'em_' . uniqid();

    if (empty(trim($iframeCode))) {
        return;
    } // nothing to show

    // ── Resolve height per breakpoint ─────────────────────────────────────────
    $presetMap = [
        'compact' => '280px',
        'standard' => '450px',
        'tall' => '600px',
        'extratall' => '750px',
        'fullscreen' => '100vh',
        'almostfull' => '90vh',
        'threequarters' => '75vh',
    ];
    $resolveH = function (array $bpCfg) use ($presetMap): string {
        if (($bpCfg['preset'] ?? '') === 'custom') {
            return max(100, (int) ($bpCfg['custom'] ?? 280)) . 'px';
        }
        return $presetMap[$bpCfg['preset'] ?? ''] ?? '450px';
    };

    $heights = $config['heights'] ?? [];
    $hPhone = $resolveH($heights['phone'] ?? ['preset' => 'compact']);
    $hTablet = $resolveH($heights['tablet'] ?? ['preset' => 'standard']);
    $hLaptop = $resolveH($heights['laptop'] ?? ['preset' => 'standard']);
    $hDesktop = $resolveH($heights['desktop'] ?? ['preset' => 'tall']);
    // ── Width mapping ─────────────────────────────────────────────────────────
    $widthCss = match ($widthType) {
        'threequarters' => '75%',
        'half' => '50%',
        'custom' => $customPct . '%',
        default => '100%',
    };

    // ── Inject width/height into the iframe code ──────────────────────────────
    // We let CSS control the height via the wrapper; the iframe gets width:100% height:100%
    $sanitized = preg_replace('/\s+width=["\'][^"\']*["\']/i', '', $iframeCode);
    $sanitized = preg_replace('/\s+height=["\'][^"\']*["\']/i', '', $sanitized);
    $sanitized = preg_replace('/<iframe/i', '<iframe class="bellevie-embed-iframe"', $sanitized, 1);
@endphp

<style>
    #{{ $embedId }} .embed-wrapper {
        height: {{ $hDesktop }};
    }

    @media (max-width:1199px) {
        #{{ $embedId }} .embed-wrapper {
            height: {{ $hLaptop }};
        }
    }

    @media (max-width:991px) {
        #{{ $embedId }} .embed-wrapper {
            height: {{ $hTablet }};
        }
    }

    @media (max-width:575px) {
        #{{ $embedId }} .embed-wrapper {
            height: {{ $hPhone }};
        }
    }

    .bellevie-embed-iframe {
        width: 100% !important;
        height: 100% !important;
        border: none;
        display: block;
    }
</style>

<section id="{{ $embedId }}" style="background:{{ $bgColor }};padding:{{ $padding }}px 0;">
    <div class="container">

        @if ($title || $description)
            <div class="text-center mb-4">
                @if ($title)
                    <h2
                        style="font-family:'Playfair Display',Georgia,serif;font-size:clamp(1.6rem,3vw,2.4rem);
                       color:#0D1B2A;margin-bottom:10px;">
                        {{ $title }}</h2>
                @endif
                @if ($description)
                    <p style="color:#666;font-size:1rem;line-height:1.7;max-width:640px;margin:0 auto;">
                        {{ $description }}
                    </p>
                @endif
            </div>
        @endif

        <div style="margin:0 auto;width:{{ $widthCss }};">
            <div class="embed-wrapper"
                style="position:relative;overflow:hidden;border-radius:8px;
                         box-shadow:0 4px 20px rgba(0,0,0,.12);">
                {!! $sanitized !!}
            </div>
        </div>

    </div>
</section>
