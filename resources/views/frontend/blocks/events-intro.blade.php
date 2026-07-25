{{--
    Events & Conference Intro block — rich description section introducing the
    hotel's events / conference offering, with optional image and highlight items.
--}}
@php
    $eyebrow       = $config['eyebrow'] ?? '';
    $title         = $config['title'] ?? '';
    $content       = $config['content'] ?? '';
    $imageUrl      = $config['imageUrl'] ?? '';
    $imagePosition = $config['imagePosition'] ?? 'right';
    $bg            = $config['bgColor'] ?? '#ffffff';
    $ctaText       = $config['ctaText'] ?? '';
    $ctaUrl        = $config['ctaUrl'] ?? '';
    $highlights    = array_values(array_filter([
        ['icon' => $config['highlight1Icon'] ?? 'bi-people',          'text' => $config['highlight1Text'] ?? ''],
        ['icon' => $config['highlight2Icon'] ?? 'bi-projector',       'text' => $config['highlight2Text'] ?? ''],
        ['icon' => $config['highlight3Icon'] ?? 'bi-cup-hot',         'text' => $config['highlight3Text'] ?? ''],
        ['icon' => $config['highlight4Icon'] ?? 'bi-calendar-check',  'text' => $config['highlight4Text'] ?? ''],
    ], fn($h) => !empty($h['text'])));

    $textCol = '
        <div>
            ' . ($eyebrow ? '<div style="color:#C9A227;font-size:0.78rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;margin-bottom:12px;">' . e($eyebrow) . '</div>' : '') . '
            ' . ($title ? '<h2 style="font-family:\'Playfair Display\',serif;font-size:2.1rem;margin-bottom:18px;color:#0D1B2A;">' . e($title) . '</h2>' : '') . '
            <div style="color:#555;line-height:1.9;font-size:0.98rem;">' . $content . '</div>
        </div>';
@endphp

<section style="background:{{ $bg }};padding:80px 0;">
    <div class="container">
        <div class="row g-5 align-items-center">
            @if($imageUrl)
            <div class="col-lg-6 {{ $imagePosition === 'left' ? 'order-lg-1' : 'order-lg-2' }}">
                <img src="{{ $imageUrl }}" alt="{{ $title }}"
                     style="width:100%;border-radius:12px;border:3px solid #C9A227;max-height:480px;object-fit:cover;">
            </div>
            <div class="col-lg-6 {{ $imagePosition === 'left' ? 'order-lg-2' : 'order-lg-1' }}">
            @else
            <div class="col-lg-8 mx-auto text-center">
            @endif
                {!! $textCol !!}

                @if(!empty($highlights))
                <div class="row g-3 mt-3 {{ $imageUrl ? '' : 'justify-content-center' }}">
                    @foreach($highlights as $h)
                    <div class="col-6 {{ $imageUrl ? '' : 'col-md-3' }}">
                        <div class="d-flex align-items-center gap-2 {{ $imageUrl ? '' : 'justify-content-center' }}">
                            <div style="width:40px;height:40px;border-radius:50%;background:#C9A22720;color:#C9A227;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi {{ $h['icon'] ?: 'bi-check-circle' }}"></i>
                            </div>
                            <div style="font-size:0.88rem;font-weight:600;color:#333;">{{ $h['text'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($ctaText && $ctaUrl)
                <div class="mt-4">
                    <a href="{{ $ctaUrl }}" class="btn text-white" style="background:#C9A227;border:none;padding:12px 32px;">
                        {{ $ctaText }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
