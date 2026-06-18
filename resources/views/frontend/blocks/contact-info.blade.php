@php
    use App\Models\Setting;

    $infoItems = [
        ['icon' => 'bi-geo-alt-fill',   'label' => 'Address',            'value' => Setting::get('hotel_address'),  'href' => null],
        ['icon' => 'bi-telephone-fill', 'label' => 'Phone',              'value' => Setting::get('hotel_phone'),    'href' => 'tel:' . preg_replace('/[^\d+]/', '', Setting::get('hotel_phone', ''))],
        ['icon' => 'bi-envelope-fill',  'label' => 'Email',              'value' => Setting::get('hotel_email'),    'href' => 'mailto:' . Setting::get('hotel_email', '')],
        ['icon' => 'bi-clock-fill',     'label' => 'Check-in / Check-out','value' => Setting::get('check_in_time','15:00') . ' – ' . Setting::get('check_out_time','11:00'), 'href' => null],
    ];
    $infoItems = array_filter($infoItems, fn($i) => !empty($i['value']));

    $socials = array_filter([
        ['icon' => 'bi-facebook',  'url' => Setting::get('facebook_url')],
        ['icon' => 'bi-instagram', 'url' => Setting::get('instagram_url')],
        ['icon' => 'bi-twitter-x', 'url' => Setting::get('twitter_url')],
    ], fn($s) => !empty($s['url']) && $s['url'] !== '#');
    $nested = $config['_nested'] ?? false;
@endphp

@if(!empty($infoItems))
@if(!$nested)<section style="padding:40px 0 60px;background:#f8f9fa;"><div class="container"><div class="row justify-content-center"><div class="col-lg-8">@endif
                <div class="card border-0 shadow-sm"
                     style="border-radius:12px;overflow:hidden;background:#0D1B2A;color:#fff;">
                    <div class="card-body p-4 p-md-5">

                        <h3 style="font-family:'Playfair Display',Georgia,serif;
                                   font-size:1.3rem;margin-bottom:28px;color:#fff;">
                            Contact Information
                        </h3>

                        <div class="row g-4">
                            @foreach($infoItems as $item)
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div style="width:44px;height:44px;border-radius:8px;flex-shrink:0;
                                                background:rgba(201,162,39,.15);
                                                display:flex;align-items:center;justify-content:center;">
                                        <i class="bi {{ $item['icon'] }}" style="color:#C9A227;font-size:1rem;"></i>
                                    </div>
                                    <div>
                                        <p style="font-size:.72rem;color:rgba(255,255,255,.45);
                                                  text-transform:uppercase;letter-spacing:.08em;margin:0 0 3px;">
                                            {{ $item['label'] }}
                                        </p>
                                        @if($item['href'])
                                        <a href="{{ $item['href'] }}"
                                           style="color:#fff;font-size:.95rem;text-decoration:none;"
                                           onmouseover="this.style.color='#C9A227'"
                                           onmouseout="this.style.color='#fff'">
                                            {{ $item['value'] }}
                                        </a>
                                        @else
                                        <p style="color:#fff;font-size:.95rem;margin:0;line-height:1.5;">
                                            {{ $item['value'] }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if(!empty($socials))
                        <div class="mt-4 pt-4" style="border-top:1px solid rgba(255,255,255,.1);">
                            <p style="font-size:.72rem;color:rgba(255,255,255,.45);
                                      text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;">
                                Follow Us
                            </p>
                            <div class="d-flex gap-2">
                                @foreach($socials as $s)
                                <a href="{{ $s['url'] }}" target="_blank" rel="noopener"
                                   style="width:40px;height:40px;border-radius:8px;
                                          background:rgba(201,162,39,.15);
                                          display:flex;align-items:center;justify-content:center;
                                          color:#C9A227;text-decoration:none;transition:background .2s;"
                                   onmouseover="this.style.background='rgba(201,162,39,.35)'"
                                   onmouseout="this.style.background='rgba(201,162,39,.15)'">
                                    <i class="bi {{ $s['icon'] }}"></i>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
@if(!$nested)</div></div></div></section>@endif
@endif
