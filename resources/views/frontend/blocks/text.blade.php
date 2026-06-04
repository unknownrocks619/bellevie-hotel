@php
    $bg      = $config['bgColor'] ?? '#ffffff';
    $padding = ($config['padding'] ?? 48) . 'px';
    $align   = $config['align'] ?? 'left';
    $maxW    = ($config['maxWidth'] ?? 800) . 'px';
@endphp
<section style="background:{{ $bg }};padding:{{ $padding }} 0;">
    <div class="container">
        <div style="max-width:{{ $maxW }};margin:0 auto;text-align:{{ $align }};">
            @if(!empty($config['title']))
            <h2 class="mb-4" style="font-family:'Playfair Display',serif;">{{ $config['title'] }}</h2>
            @endif
            <div style="color:#555;line-height:1.9;font-size:1rem;">
                {!! $config['content'] ?? '' !!}
            </div>
        </div>
    </div>
</section>
