@php
    $style   = $config['style']   ?? 'line';
    $color   = $config['color']   ?? '#dee2e6';
    $spacing = (int)($config['spacing'] ?? 40);
@endphp
<div style="padding:{{ $spacing }}px 0;">
    @if($style === 'line')
        <hr style="border:none;border-top:1px solid {{ $color }};margin:0;">
    @elseif($style === 'thick')
        <hr style="border:none;border-top:3px solid {{ $color }};margin:0;">
    @elseif($style === 'dots')
        <div class="text-center" style="color:{{ $color }};font-size:1.2rem;letter-spacing:0.5em;">• • •</div>
    @elseif($style === 'ornament')
        <div class="text-center" style="color:#C9A227;font-size:1.4rem;letter-spacing:0.3em;">— ✦ —</div>
    @elseif($style === 'wave')
        <div style="text-align:center;color:{{ $color }};font-size:1.1rem;letter-spacing:0.2em;">〜〜〜〜〜</div>
    @endif
    {{-- 'blank' renders nothing inside, just the padding --}}
</div>
