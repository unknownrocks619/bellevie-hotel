@php
    $query = \App\Models\Gallery::where('is_active', true)->orderBy('sort_order');
    if (!empty($config['category'])) $query->where('category', $config['category']);
    $images = $query->limit((int)($config['count'] ?? 6))->get();
    $cols = (int)($config['columns'] ?? 3);
    $bsCols = 12 / $cols;
@endphp
<section class="py-5">
    <div class="container">
        @if(!empty($config['title']))
        <div class="text-center mb-5">
            <h2 style="font-family:'Playfair Display',serif;">{{ $config['title'] }}</h2>
        </div>
        @endif
        <div class="row g-3">
            @foreach($images as $img)
            <div class="col-{{ $bsCols }}">
                <img src="{{ $img->image_url }}" alt="{{ $img->alt_text ?? $img->title }}"
                     class="img-fluid w-100" style="aspect-ratio:1;object-fit:cover;border-radius:8px;">
            </div>
            @endforeach
        </div>
    </div>
</section>
