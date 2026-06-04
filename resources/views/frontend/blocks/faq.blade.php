@php
    use App\Models\Faq;

    $category  = $config['category']  ?? 'all';
    $layout    = $config['layout']    ?? 'expandable';
    $maxItems  = (int) ($config['maxItems'] ?? 10);
    $bgColor   = $config['bgColor']   ?? '#ffffff';
    $title     = $config['title']     ?? '';
    $subtitle  = $config['subtitle']  ?? '';

    $query = Faq::active()->orderBy('sort_order')->orderBy('title');
    if ($category && $category !== 'all') {
        $query->where('category', $category);
    }
    $faqs = $query->take($maxItems)->get();
@endphp

@if($faqs->isNotEmpty())
<section style="background:{{ $bgColor }}; padding: 64px 0;">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 24px;">

        @if($title)
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-family: Georgia, serif; color: #0D1B2A; margin-bottom: 10px;">{{ $title }}</h2>
            @if($subtitle)
            <p style="color: #666; font-size: 1rem; margin: 0;">{{ $subtitle }}</p>
            @endif
        </div>
        @endif

        @if($layout === 'expandable')
        {{-- Expandable / Accordion style --}}
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($faqs as $i => $faq)
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.06);">
                <button
                    onclick="(function(btn){
                        var body = btn.nextElementSibling;
                        var icon = btn.querySelector('.faq-icon');
                        var isOpen = body.style.display !== 'none';
                        body.style.display = isOpen ? 'none' : 'block';
                        icon.textContent = isOpen ? '+' : '−';
                        btn.style.background = isOpen ? '#fff' : '#fdf8ea';
                    })(this)"
                    style="width:100%; display:flex; justify-content:space-between; align-items:center;
                           padding: 18px 20px; background: {{ $i === 0 ? '#fdf8ea' : '#fff' }};
                           border: none; cursor: pointer; text-align: left; font-family: inherit;
                           border-bottom: {{ $i === 0 ? '1px solid #f0ead6' : 'none' }};">
                    <span style="font-weight: 600; font-size: 0.95rem; color: #1a1a2e; line-height: 1.4;">{{ $faq->title }}</span>
                    <span class="faq-icon" style="color: #C9A227; font-size: 1.3rem; font-weight: 300; flex-shrink: 0; margin-left: 16px; line-height: 1;">{{ $i === 0 ? '−' : '+' }}</span>
                </button>
                <div style="display: {{ $i === 0 ? 'block' : 'none' }}; padding: 16px 20px 20px; color: #555; font-size: 0.9rem; line-height: 1.7; border-top: 1px solid #f0f0f0;">
                    {!! nl2br(e($faq->description)) !!}
                </div>
            </div>
            @endforeach
        </div>

        @else
        {{-- Simple rows — always shows title + description --}}
        <div>
            @foreach($faqs as $i => $faq)
            <div style="padding: 24px 0; {{ $i > 0 ? 'border-top: 1px solid #eee;' : '' }}">
                <h4 style="font-size: 1rem; font-weight: 700; color: #1a1a2e; margin: 0 0 10px;">{{ $faq->title }}</h4>
                <div style="color: #555; font-size: 0.9rem; line-height: 1.7; margin: 0;">
                    {!! nl2br(e($faq->description)) !!}
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</section>
@endif
