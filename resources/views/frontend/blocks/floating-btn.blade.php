@php
    $position    = $config['position'] ?? 'bottom-right';
    $mobileBottom = $config['mobileAlwaysBottom'] ?? true;
    $label       = $config['label'] ?? 'Book Now';
    $url         = $config['url'] ?? '/booking';
    $icon        = $config['icon'] ?? 'bi-calendar-check';
    $bg          = $config['bgColor'] ?? '#C9A227';
    $color       = $config['textColor'] ?? '#ffffff';
    $isLarge     = ($config['size'] ?? 'normal') === 'large';
    $show        = $config['desktopShow'] ?? true;

    // Position CSS
    $posMap = [
        'bottom-right'  => 'bottom:24px;right:24px;',
        'bottom-left'   => 'bottom:24px;left:24px;',
        'bottom-center' => 'bottom:24px;left:50%;transform:translateX(-50%);',
        'top-right'     => 'top:80px;right:24px;',
        'top-left'      => 'top:80px;left:24px;',
    ];
    $posStyle = $posMap[$position] ?? $posMap['bottom-right'];
    $pad = $isLarge ? '14px 28px' : '10px 20px';
    $fs  = $isLarge ? '1rem' : '0.88rem';
@endphp

{{-- Floating Button --}}
<style>
.bellevie-float-btn {
    position: fixed;
    {{ $posStyle }}
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 8px;
    background: {{ $bg }};
    color: {{ $color }};
    padding: {{ $pad }};
    border-radius: 50px;
    font-size: {{ $fs }};
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 20px rgba(0,0,0,.25);
    transition: transform .2s, box-shadow .2s;
    {{ !$show ? 'display:none!important;' : '' }}
}
.bellevie-float-btn:hover {
    transform: translateY(-2px) scale(1.04);
    box-shadow: 0 8px 28px rgba(0,0,0,.35);
    color: {{ $color }};
}

/* Mobile: always stick to bottom bar */
@if($mobileBottom)
@media (max-width: 767px) {
    .bellevie-float-btn {
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        top: auto !important;
        transform: none !important;
        border-radius: 0;
        justify-content: center;
        width: 100%;
        padding: 14px 20px;
        font-size: 1rem;
        box-shadow: 0 -2px 12px rgba(0,0,0,.15);
    }
    .bellevie-float-btn:hover {
        transform: none;
    }
    /* Push page content up so the bar doesn't cover it */
    body { padding-bottom: 56px; }
}
@endif
</style>

<a href="{{ $url }}" class="bellevie-float-btn" title="{{ $label }}">
    @if($icon)
    <i class="bi {{ $icon }}"></i>
    @endif
    <span>{{ $label }}</span>
</a>
