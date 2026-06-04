@php
    $perRow   = (int)($config['perRow'] ?? 3);
    $maxRows  = $config['maxRows'] ?? '1';
    $count    = $maxRows === 'all' ? 99 : (int)$maxRows * $perRow;
    $symbol   = \App\Models\Setting::get('currency_symbol', '$');
    $btnText  = $config['btnText'] ?? 'View Room';
    $btnUrl   = $config['btnUrl'] ?? '';

    $query = \App\Models\Room::with('roomType')->active();
    if (!empty($config['featuredOnly'])) $query->where('is_featured', true);
    $rooms = $query->limit($count)->get();

    // Bootstrap column class based on perRow
    $colClass = match($perRow) {
        1 => 'col-12',
        2 => 'col-md-6',
        4 => 'col-sm-6 col-lg-3',
        default => 'col-md-6 col-lg-4',
    };
@endphp
<section class="py-5" style="background:#fff;">
    <div class="container">
        @if(!empty($config['title']))
        <div class="text-center mb-5">
            <h2 style="font-family:'Playfair Display',serif;font-size:2.2rem;">{{ $config['title'] }}</h2>
            @if(!empty($config['subtitle']))<p class="text-muted">{{ $config['subtitle'] }}</p>@endif
            <div style="width:60px;height:3px;background:#C9A227;margin:14px auto 0;"></div>
        </div>
        @endif

        <div class="row g-4">
            @foreach($rooms as $room)
            <div class="{{ $colClass }}">
                <div class="card h-100" style="border:none;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08);transition:transform .2s,box-shadow .2s;"
                     onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,.14)'"
                     onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'">

                    <div style="position:relative;overflow:hidden;">
                        <img src="{{ $room->featuredImageUrl('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&q=60') }}"
                             class="card-img-top" alt="{{ $room->name }}"
                             style="height:{{ $perRow === 1 ? '400px' : '220px' }};object-fit:cover;transition:transform .4s;"
                             onmouseenter="this.style.transform='scale(1.05)'"
                             onmouseleave="this.style.transform='scale(1)'">
                        @if($room->is_featured)
                        <span style="position:absolute;top:12px;right:12px;background:#C9A227;color:#fff;font-size:0.7rem;padding:4px 10px;border-radius:20px;font-weight:600;">
                            Featured
                        </span>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <div class="mb-1">
                            <span style="font-size:0.72rem;color:#C9A227;font-weight:600;text-transform:uppercase;letter-spacing:.08em;">
                                {{ $room->roomType->name ?? 'Standard' }}
                            </span>
                        </div>
                        <h5 class="card-title mb-2" style="font-family:'Playfair Display',serif;">{{ $room->name }}</h5>

                        @if($config['showDescription'] ?? true)
                        <p class="text-muted small mb-2" style="line-height:1.6;flex:1;">
                            {{ Str::limit($room->description, $perRow === 1 ? 200 : 90) }}
                        </p>
                        @endif

                        <div class="d-flex align-items-center gap-3 mb-3 mt-auto">
                            @if($room->bed_type)
                            <small class="text-muted"><i class="bi bi-moon me-1"></i>{{ $room->bed_type }}</small>
                            @endif
                            @if($room->max_adults)
                            <small class="text-muted"><i class="bi bi-people me-1"></i>{{ $room->max_adults + ($room->max_children ?? 0) }}</small>
                            @endif
                            @if($room->size_sqft)
                            <small class="text-muted"><i class="bi bi-arrows-fullscreen me-1"></i>{{ $room->size_sqft }} ft²</small>
                            @endif
                        </div>

                        @if($config['showPrice'] ?? true)
                        <div class="mb-3" style="color:#C9A227;font-weight:700;font-size:1.1rem;font-family:'Playfair Display',serif;">
                            {{ $symbol }}{{ number_format($room->price_per_night) }}
                            <small style="color:#999;font-weight:400;font-size:0.8rem;font-family:inherit;">/night</small>
                        </div>
                        @endif
                    </div>

                    @if(!empty($btnText))
                    <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
                        <a href="{{ $btnUrl ?: route('rooms.show', $room) }}"
                           class="btn w-100" style="background:#C9A227;color:#fff;border:none;border-radius:6px;">
                            {{ $btnText }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
