<section style="padding:0 0 60px;background:#f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                    <div class="card-body p-4">
                        <h4 style="font-size:1rem;font-weight:700;color:#0D1B2A;margin-bottom:16px;">
                            Quick Links
                        </h4>
                        <div class="d-flex flex-column gap-2">
                            @foreach([
                                ['label' => 'Book a Room',        'href' => route('booking.create'), 'icon' => 'bi-calendar-check'],
                                ['label' => 'View Our Rooms',     'href' => route('rooms.index'),    'icon' => 'bi-door-open'],
                                ['label' => 'Check Availability', 'href' => route('booking.check'),  'icon' => 'bi-search'],
                            ] as $link)
                            <a href="{{ $link['href'] }}"
                               class="d-flex align-items-center gap-3 p-3 text-decoration-none"
                               style="border:1px solid #eee;border-radius:8px;color:#333;background:#fff;
                                      transition:border-color .15s,background .15s;"
                               onmouseover="this.style.borderColor='#C9A227';this.style.background='#fdf8ea'"
                               onmouseout="this.style.borderColor='#eee';this.style.background='#fff'">
                                <i class="bi {{ $link['icon'] }}" style="color:#C9A227;font-size:1.1rem;"></i>
                                <span style="font-size:.9rem;font-weight:500;">{{ $link['label'] }}</span>
                                <i class="bi bi-chevron-right ms-auto" style="color:#ccc;font-size:.75rem;"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
