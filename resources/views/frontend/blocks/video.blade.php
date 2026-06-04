@php
    $videoType   = $config['videoType'] ?? 'youtube';
    $videoUrl    = $config['videoUrl'] ?? '';
    $controls    = $config['controls'] ?? true;
    $autoplay    = $config['autoplay'] ?? false;
    $muted       = $config['muted'] ?? true;
    $loop        = $config['loop'] ?? false;
    $aspectRatio = $config['aspectRatio'] ?? '16/9';
    $maxWidth    = ($config['maxWidth'] ?? 900) . 'px';
    $bg          = $config['bgColor'] ?? '#000000';

    // Extract YouTube ID
    $ytId = '';
    if ($videoType === 'youtube' && $videoUrl) {
        preg_match('/(?:v=|youtu\.be\/|embed\/|shorts\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $m);
        $ytId = $m[1] ?? '';
    }

    // Build YouTube embed URL
    $ytParams = http_build_query(array_filter([
        'rel'      => 0,
        'controls' => $controls ? 1 : 0,
        'autoplay' => $autoplay ? 1 : 0,
        'mute'     => ($autoplay || $muted) ? 1 : 0,
        'loop'     => $loop ? 1 : 0,
        'playlist' => $loop ? $ytId : null,
    ]));
@endphp

<section style="background:{{ $bg }};padding:{{ (empty($config['title']) && empty($config['subtitle'])) ? '0' : '48px 0' }};">
    <div class="container">
        @if(!empty($config['title']) || !empty($config['subtitle']))
        <div class="text-center mb-4" style="color:{{ $bg === '#000000' || $bg === '#0D1B2A' ? '#fff' : '#333' }};">
            @if(!empty($config['title']))
            <h2 style="font-family:'Playfair Display',serif;">{{ $config['title'] }}</h2>
            @endif
            @if(!empty($config['subtitle']))
            <p style="opacity:.75;">{{ $config['subtitle'] }}</p>
            @endif
        </div>
        @endif

        <div style="max-width:{{ $maxWidth }};margin:0 auto;">
            <div style="position:relative;padding-bottom:calc(100% / ({{ str_replace('/', ' / ', $aspectRatio) }}));height:0;border-radius:8px;overflow:hidden;background:#111;">

                @if($videoType === 'youtube' && $ytId)
                <iframe src="https://www.youtube-nocookie.com/embed/{{ $ytId }}?{{ $ytParams }}"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;"
                        title="{{ $config['title'] ?? 'Video' }}">
                </iframe>

                @elseif($videoType === 'cloudinary' && $videoUrl)
                <video style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;"
                       {{ $controls ? 'controls' : '' }}
                       {{ $autoplay ? 'autoplay' : '' }}
                       {{ $muted || $autoplay ? 'muted' : '' }}
                       {{ $loop ? 'loop' : '' }}
                       playsinline>
                    <source src="{{ $videoUrl }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>

                @else
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#666;">
                    <i class="bi bi-play-circle" style="font-size:3rem;margin-bottom:8px;"></i>
                    <small>No video URL configured</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
