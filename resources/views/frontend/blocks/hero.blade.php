@php
    $bg      = !empty($config['backgroundImageUrl'])
        ? "url('{$config['backgroundImageUrl']}') center/cover"
        : "linear-gradient(135deg, #0D1B2A 0%, #1a3a5c 100%)";
    $align   = $config['textAlign'] ?? 'center';
    $overlay = $config['overlay'] ?? 0.5;

    // Responsive min-heights: desktop → tablet → mobile with sensible fallbacks
    $hLg = (int) ($config['minHeight']   ?? 500);
    $hMd = (int) ($config['minHeightMd'] ?? max(280, (int) ($hLg * 0.76)));
    $hSm = (int) ($config['minHeightSm'] ?? max(220, (int) ($hMd * 0.74)));

    $heroUid = 'hb' . substr(md5(serialize($config)), 0, 8);
@endphp

<style>
    .{{ $heroUid }}          { min-height: {{ $hLg }}px; }
    @media (max-width:991px) { .{{ $heroUid }} { min-height: {{ $hMd }}px; } }
    @media (max-width:575px) { .{{ $heroUid }} { min-height: {{ $hSm }}px; } }
</style>

<section class="{{ $heroUid }}" style="background:{{ $bg }};display:flex;align-items:center;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,{{ $overlay }});"></div>
    <div class="container" style="position:relative;z-index:1;text-align:{{ $align }};padding:80px 20px;">
        @if(!empty($config['title']))
        <h1 class="text-white mb-3" style="font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);">
            {{ $config['title'] }}
        </h1>
        @endif
        @if(!empty($config['subtitle']))
        <p class="mb-4" style="color:rgba(255,255,255,.85);font-size:1.15rem;max-width:600px;margin-left:{{ $align==='center'?'auto':0 }};margin-right:{{ $align==='center'?'auto':0 }};">
            {{ $config['subtitle'] }}
        </p>
        @endif
        <div class="d-flex gap-3 flex-wrap {{ $align==='center'?'justify-content-center':($align==='right'?'justify-content-end':'') }}">
            @if(!empty($config['ctaText']))
            <a href="{{ $config['ctaUrl'] ?? '#' }}" class="btn btn-lg px-5" style="background:#C9A227;color:#fff;border:none;">
                {{ $config['ctaText'] }}
            </a>
            @endif
            @if(!empty($config['ctaText2']))
            <a href="{{ $config['ctaUrl2'] ?? '#' }}" class="btn btn-lg px-5" style="background:transparent;color:#fff;border:2px solid rgba(255,255,255,.8);">
                {{ $config['ctaText2'] }}
            </a>
            @endif
        </div>
    </div>
</section>
