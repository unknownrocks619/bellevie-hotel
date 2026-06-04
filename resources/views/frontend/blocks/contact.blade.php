@php
    $s = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
    $bg = $config['bgColor'] ?? '#ffffff';
@endphp
<section style="background:{{ $bg }};padding:72px 0;">
    <div class="container">
        @if(!empty($config['title']))
        <h2 class="text-center mb-5" style="font-family:'Playfair Display',serif;">{{ $config['title'] }}</h2>
        @endif
        <div class="row g-5 align-items-start">
            <div class="{{ ($config['showMap'] ?? true) ? 'col-lg-5' : 'col-lg-8 mx-auto' }}">
                <div class="d-flex flex-column gap-4 mb-4">
                    @if(!empty($s['hotel_address']))
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-geo-alt-fill mt-1" style="color:#C9A227;font-size:1.2rem;flex-shrink:0;"></i>
                        <div>
                            <div class="fw-semibold mb-1">Address</div>
                            <div class="text-muted">{{ $s['hotel_address'] }}@if(!empty($s['hotel_city'])), {{ $s['hotel_city'] }}@endif</div>
                        </div>
                    </div>
                    @endif
                    @if(!empty($s['hotel_phone']))
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-telephone-fill mt-1" style="color:#C9A227;font-size:1.2rem;flex-shrink:0;"></i>
                        <div>
                            <div class="fw-semibold mb-1">Phone</div>
                            <div class="text-muted"><a href="tel:{{ $s['hotel_phone'] }}" class="text-muted text-decoration-none">{{ $s['hotel_phone'] }}</a></div>
                        </div>
                    </div>
                    @endif
                    @if(!empty($s['hotel_email']))
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-envelope-fill mt-1" style="color:#C9A227;font-size:1.2rem;flex-shrink:0;"></i>
                        <div>
                            <div class="fw-semibold mb-1">Email</div>
                            <div class="text-muted"><a href="mailto:{{ $s['hotel_email'] }}" class="text-muted text-decoration-none">{{ $s['hotel_email'] }}</a></div>
                        </div>
                    </div>
                    @endif
                    @if(!empty($s['check_in_time']))
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-clock-fill mt-1" style="color:#C9A227;font-size:1.2rem;flex-shrink:0;"></i>
                        <div>
                            <div class="fw-semibold mb-1">Hours</div>
                            <div class="text-muted">Check-in: {{ $s['check_in_time'] }} · Check-out: {{ $s['check_out_time'] ?? '' }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                @if($config['showForm'] ?? false)
                <form action="{{ route('contact.send') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="message" class="form-control" rows="4" placeholder="Your message…" required></textarea>
                    </div>
                    <button type="submit" class="btn w-100" style="background:#C9A227;color:#fff;border:none;">
                        Send Message
                    </button>
                </form>
                @endif
            </div>

            @if($config['showMap'] ?? true)
            <div class="col-lg-7">
                <div class="rounded overflow-hidden" style="height:320px;background:#e8edf2;display:flex;align-items:center;justify-content:center;border:1px solid #dee2e6;">
                    {{-- Replace with actual embed if coordinates are available --}}
                    <div class="text-center text-muted">
                        <i class="bi bi-map" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>
                        <small>{{ $s['hotel_city'] ?? 'Hotel Location' }}</small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
