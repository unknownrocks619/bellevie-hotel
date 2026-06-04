@php
    $bgType    = $config['bgType'] ?? 'color';
    $overlay   = $config['overlay'] ?? 0.6;
    $minH      = ($config['minHeight'] ?? 400) . 'px';
    $textColor = $config['textColor'] ?? '#ffffff';
    $ytId = '';
    if ($bgType === 'video' && ($config['bgVideoType'] ?? '') === 'youtube' && !empty($config['bgVideoUrl'])) {
        preg_match('/(?:v=|youtu\.be\/|embed\/|shorts\/)([a-zA-Z0-9_-]{11})/', $config['bgVideoUrl'], $m);
        $ytId = $m[1] ?? '';
    }
@endphp
<section style="position:relative;min-height:{{ $minH }};display:flex;align-items:center;overflow:hidden;{{ $bgType==='color' ? 'background:'.($config['bgColor']??'#0D1B2A').';' : '' }}{{ ($bgType==='image'&&!empty($config['bgImageUrl'])) ? 'background:url('.$config['bgImageUrl'].') center/cover no-repeat;' : '' }}">

    @if($bgType === 'video')
        @if(($config['bgVideoType']??'') === 'youtube' && $ytId)
        <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;">
            <iframe src="https://www.youtube-nocookie.com/embed/{{ $ytId }}?autoplay=1&mute=1&loop=1&playlist={{ $ytId }}&controls=0&showinfo=0&rel=0"
                    allow="autoplay;encrypted-media"
                    style="position:absolute;top:50%;left:50%;min-width:100%;min-height:100%;width:auto;height:auto;transform:translate(-50%,-50%);border:none;"
                    title="bg video"></iframe>
        </div>
        @elseif(($config['bgVideoType']??'') === 'cloudinary' && !empty($config['bgVideoUrl']))
        <video autoplay muted loop playsinline style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
            <source src="{{ $config['bgVideoUrl'] }}" type="video/mp4">
        </video>
        @else
        <div style="position:absolute;inset:0;background:#0D1B2A;"></div>
        @endif
    @endif

    <div style="position:absolute;inset:0;background:rgba(0,0,0,{{ $overlay }});"></div>

    <div class="container" style="position:relative;z-index:1;text-align:center;padding:60px 20px;">
        @if(!empty($config['title']))
        <h2 style="font-family:'Playfair Display',serif;font-size:2.4rem;color:{{ $textColor }};margin-bottom:16px;">{{ $config['title'] }}</h2>
        @endif
        @if(!empty($config['subtitle']))
        <p style="color:{{ $textColor }};opacity:.85;font-size:1.1rem;margin-bottom:32px;max-width:600px;margin-left:auto;margin-right:auto;">{{ $config['subtitle'] }}</p>
        @endif
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            @if(!empty($config['ctaText']))
            <a href="{{ $config['ctaUrl']??'#' }}" class="btn btn-lg px-5" style="background:#C9A227;color:#fff;border:none;border-radius:4px;">{{ $config['ctaText'] }}</a>
            @endif
            @if(!empty($config['ctaText2']))
            <a href="{{ $config['ctaUrl2']??'#' }}" class="btn btn-lg px-5" style="background:transparent;color:{{ $textColor }};border:2px solid {{ $textColor }};border-radius:4px;">{{ $config['ctaText2'] }}</a>
            @endif
        </div>
    </div>
</section>
