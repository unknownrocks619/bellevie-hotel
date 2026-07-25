@php
    use App\Models\Event;

    $typeFilter   = $config['typeFilter'] ?? 'all';
    $count        = (int) ($config['count'] ?? 3);
    $columns      = (int) ($config['columns'] ?? 3);
    $upcomingOnly = $config['upcomingOnly'] ?? true;
    $featuredOnly = $config['featuredOnly'] ?? false;
    $showDate     = $config['showDate'] ?? true;
    $showVenue    = $config['showVenue'] ?? true;
    $showType     = $config['showType'] ?? true;
    $bg           = $config['bgColor'] ?? '#ffffff';
    $btnText      = $config['btnText'] ?? 'View Details';
    $viewAllText  = $config['viewAllText'] ?? '';

    $query = Event::active()->orderBy('starts_at')->orderBy('sort_order');
    if ($typeFilter !== 'all' && array_key_exists($typeFilter, Event::TYPES)) {
        $query->byType($typeFilter);
    }
    if ($upcomingOnly) {
        $query->upcoming();
    }
    if ($featuredOnly) {
        $query->where('is_featured', true);
    }
    $events = $query->take(max(1, $count))->get();
    $bsCols = 12 / max(1, min(4, $columns));
@endphp

@if($events->isNotEmpty())
<section style="background:{{ $bg }};padding:80px 0;">
    <div class="container">
        @if(!empty($config['title']))
        <div class="text-center mb-5">
            <h2 style="font-family:'Playfair Display',serif;font-size:2.2rem;">{{ $config['title'] }}</h2>
            @if(!empty($config['subtitle']))
            <p style="color:#888;margin-top:8px;">{{ $config['subtitle'] }}</p>
            @endif
            <div style="width:60px;height:3px;background:#C9A227;margin:14px auto 0;"></div>
        </div>
        @endif

        <div class="row g-4">
            @foreach($events as $event)
            <div class="col-sm-6 col-lg-{{ $bsCols }}">
                <div class="h-100" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);display:flex;flex-direction:column;">
                    <a href="{{ route('events.show', $event) }}" style="display:block;position:relative;">
                        @if($event->image_url)
                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" style="width:100%;height:190px;object-fit:cover;">
                        @else
                        <div style="width:100%;height:190px;background:linear-gradient(135deg,#0D1B2A,#1a3a5c);display:flex;align-items:center;justify-content:center;color:#C9A227;font-size:2.2rem;">
                            <i class="bi {{ $event->type === 'conference' ? 'bi-people' : 'bi-calendar-event' }}"></i>
                        </div>
                        @endif
                        @if($showType)
                        <span style="position:absolute;top:12px;left:12px;background:#C9A227;color:#fff;padding:4px 12px;border-radius:50px;font-size:0.7rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;">
                            {{ $event->type_label }}
                        </span>
                        @endif
                    </a>
                    <div class="p-4" style="flex:1;display:flex;flex-direction:column;">
                        @if($showDate && $event->date_range)
                        <div style="color:#C9A227;font-size:0.8rem;font-weight:600;margin-bottom:8px;">
                            <i class="bi bi-calendar3 me-1"></i>{{ $event->date_range }}
                        </div>
                        @endif
                        <h5 style="font-family:'Playfair Display',serif;margin-bottom:8px;">
                            <a href="{{ route('events.show', $event) }}" style="color:#0D1B2A;text-decoration:none;">{{ $event->title }}</a>
                        </h5>
                        <p style="color:#666;font-size:0.88rem;line-height:1.6;flex:1;">
                            {{ Str::limit($event->excerpt ?: strip_tags($event->description), 100) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            @if($showVenue && $event->venue)
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($event->venue, 22) }}</small>
                            @else
                            <span></span>
                            @endif
                            @if($btnText)
                            <a href="{{ route('events.show', $event) }}" style="color:#C9A227;font-size:0.85rem;font-weight:600;text-decoration:none;">
                                {{ $btnText }} <i class="bi bi-arrow-right"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($viewAllText)
        <div class="text-center mt-5">
            <a href="{{ route('events.index', $typeFilter !== 'all' ? ['type' => $typeFilter] : []) }}"
               class="btn btn-lg" style="border:2px solid #C9A227;color:#C9A227;padding:10px 34px;">
                {{ $viewAllText }}
            </a>
        </div>
        @endif
    </div>
</section>
@endif
