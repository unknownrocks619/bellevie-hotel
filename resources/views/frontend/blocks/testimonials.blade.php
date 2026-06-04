@php
    $testimonials = \App\Models\Testimonial::where('is_active', true)
        ->orderBy('sort_order')->limit((int)($config['count'] ?? 6))->get();
    $layout     = $config['layout'] ?? 'grid';
    $columns    = (int)($config['columns'] ?? 3);
    $bg         = $config['bgColor'] ?? '#f5f0e8';
    $showAvatar = $config['showAvatar'] ?? true;
    $bsCols     = 12 / max(1, $columns);
    $sliderId   = 'testimonialSlider_' . Str::random(6);
@endphp
<section style="background:{{ $bg }};padding:80px 0;">
    <div class="container">
        @if(!empty($config['title']))
        <div class="text-center mb-5">
            <h2 style="font-family:'Playfair Display',serif;font-size:2.2rem;">{{ $config['title'] }}</h2>
            <div style="width:60px;height:3px;background:#C9A227;margin:14px auto 0;"></div>
        </div>
        @endif

        @if($layout === 'slider')
        <div id="{{ $sliderId }}" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" style="position:relative;">
            <div class="carousel-inner">
                @foreach($testimonials as $i => $t)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                    <div style="max-width:720px;margin:0 auto;text-align:center;padding:0 60px;">
                        <div style="color:#C9A227;font-size:1.3rem;margin-bottom:16px;">
                            @for($s = 0; $s < ($t->rating ?? 5); $s++)★@endfor
                        </div>
                        <p style="font-size:1.05rem;line-height:1.9;color:#444;font-style:italic;margin-bottom:28px;">
                            "{{ $t->content }}"
                        </p>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            @if($showAvatar)
                                @if($t->guest_avatar)
                                <img src="{{ $t->guest_avatar }}" alt="{{ $t->guest_name }}"
                                     style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid #C9A227;">
                                @else
                                <div style="width:52px;height:52px;border-radius:50%;background:#C9A22730;display:flex;align-items:center;justify-content:center;color:#C9A227;font-size:1.2rem;font-weight:700;">
                                    {{ strtoupper(substr($t->guest_name, 0, 1)) }}
                                </div>
                                @endif
                            @endif
                            <div style="text-align:left;">
                                <div style="font-weight:700;">{{ $t->guest_name }}</div>
                                @if(($config['showCountry'] ?? true) && $t->guest_country)
                                <div style="color:#999;font-size:0.82rem;">{{ $t->guest_country }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#{{ $sliderId }}" data-bs-slide="prev"
                    style="width:44px;height:44px;background:#C9A227;border-radius:50%;top:50%;transform:translateY(-50%);left:0;opacity:1;position:absolute;">
                <i class="bi bi-chevron-left text-white"></i>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#{{ $sliderId }}" data-bs-slide="next"
                    style="width:44px;height:44px;background:#C9A227;border-radius:50%;top:50%;transform:translateY(-50%);right:0;opacity:1;position:absolute;">
                <i class="bi bi-chevron-right text-white"></i>
            </button>
            <div class="carousel-indicators position-relative mt-4" style="bottom:0;">
                @foreach($testimonials as $i => $t)
                <button type="button" data-bs-target="#{{ $sliderId }}" data-bs-slide-to="{{ $i }}"
                        class="{{ $i === 0 ? 'active' : '' }}"
                        style="background:#C9A227;width:8px;height:8px;border-radius:50%;border:none;opacity:{{ $i===0?1:.35 }};margin:0 4px;"></button>
                @endforeach
            </div>
        </div>
        @else
        <div class="row g-4">
            @foreach($testimonials as $t)
            <div class="col-sm-6 col-lg-{{ $bsCols }}">
                <div class="h-100 p-4" style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.07);display:flex;flex-direction:column;">
                    <div style="color:#C9A227;font-size:1rem;margin-bottom:10px;">
                        @for($s = 0; $s < ($t->rating ?? 5); $s++)★@endfor
                    </div>
                    <p style="color:#555;font-style:italic;line-height:1.7;flex:1;margin-bottom:16px;">
                        "{{ $t->content }}"
                    </p>
                    <div class="d-flex align-items-center gap-3 mt-auto">
                        @if($showAvatar)
                            @if($t->guest_avatar)
                            <img src="{{ $t->guest_avatar }}" alt="{{ $t->guest_name }}"
                                 style="width:42px;height:42px;border-radius:50%;object-fit:cover;border:2px solid #C9A22740;flex-shrink:0;">
                            @else
                            <div style="width:42px;height:42px;border-radius:50%;background:#C9A22720;display:flex;align-items:center;justify-content:center;color:#C9A227;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($t->guest_name, 0, 1)) }}
                            </div>
                            @endif
                        @endif
                        <div>
                            <div style="font-weight:700;font-size:0.88rem;">{{ $t->guest_name }}</div>
                            @if(($config['showCountry'] ?? true) && $t->guest_country)
                            <div style="color:#aaa;font-size:0.75rem;">{{ $t->guest_country }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
