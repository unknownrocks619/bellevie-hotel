@php
    $features = $config['features'] ?? [];
    $columns  = (int)($config['columns'] ?? 3);
    $bsCols   = 12 / $columns;
    $bg       = $config['bgColor'] ?? '#f5f0e8';
@endphp
<section style="background:{{ $bg }};padding:80px 0;">
    <div class="container">
        @if(!empty($config['title']))
        <div class="text-center mb-5">
            <h2 style="font-family:'Playfair Display',serif;font-size:2.2rem;">{{ $config['title'] }}</h2>
            @if(!empty($config['subtitle']))
            <p class="text-muted mt-2">{{ $config['subtitle'] }}</p>
            @endif
            <div style="width:60px;height:3px;background:#C9A227;margin:16px auto 0;"></div>
        </div>
        @endif

        <div class="row g-4">
            @foreach($features as $feature)
            <div class="col-sm-6 col-lg-{{ $bsCols }}">
                <div class="text-center p-4 h-100"
                     style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.06);transition:transform .2s,box-shadow .2s;"
                     onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.12)'"
                     onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(0,0,0,.06)'">
                    <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#C9A22720,#C9A22740);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="bi {{ $feature['icon'] ?? 'bi-star' }}" style="font-size:1.5rem;color:#C9A227;"></i>
                    </div>
                    @if(!empty($feature['title']))
                    <h5 class="mb-2" style="font-family:'Playfair Display',serif;">{{ $feature['title'] }}</h5>
                    @endif
                    @if(!empty($feature['desc']))
                    <p class="text-muted mb-0" style="font-size:0.88rem;line-height:1.7;">{{ $feature['desc'] }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
