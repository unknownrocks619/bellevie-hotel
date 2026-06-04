@extends('layouts.app')
@section('title', 'Contact Us — ' . ($settings['hotel_name'] ?? 'Bellevie Hotel'))

@section('content')

@if(!empty($sections))

    {{-- ── Builder-driven contact page ────────────────────────────────────── --}}
    @include('frontend.builder-content', ['sections' => $sections])

@else

    {{-- ── Static fallback (shown until builder data is saved) ────────────── --}}

    {{-- Page Hero --}}
    <div style="background:linear-gradient(135deg,#0D1B2A 0%,#1a3a5c 100%);
                padding:120px 0 64px;text-align:center;color:#fff;">
        <div class="container">
            <p style="color:#C9A227;font-size:0.78rem;font-weight:700;letter-spacing:.14em;
                      text-transform:uppercase;margin-bottom:14px;">GET IN TOUCH</p>
            <h1 style="font-family:'Playfair Display',Georgia,serif;
                       font-size:clamp(2rem,4vw,3rem);font-weight:700;
                       margin-bottom:16px;line-height:1.2;">Contact Us</h1>
            <p style="opacity:.8;font-size:1.05rem;max-width:520px;margin:0 auto;line-height:1.7;">
                We'd love to hear from you. Reach out with any questions, reservations or special requests.
            </p>
        </div>
    </div>

    {{-- Main content --}}
    <section style="padding:72px 0;background:#f8f9fa;">
        <div class="container">
            <div class="row g-5">

                {{-- Contact Form --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                        <div class="card-body p-4 p-md-5">

                            <h2 style="font-family:'Playfair Display',Georgia,serif;
                                       font-size:1.6rem;color:#0D1B2A;margin-bottom:6px;">
                                Send us a Message
                            </h2>
                            <p class="text-muted mb-4" style="font-size:.92rem;">
                                Fill in the form below and we'll get back to you within 24 hours.
                            </p>

                            @if(session('contact_success'))
                            <div class="d-flex align-items-center gap-2 mb-4 p-3"
                                 style="border-left:4px solid #C9A227;background:#fdf8ea;
                                        border-radius:6px;color:#5a4500;">
                                <i class="bi bi-check-circle-fill" style="color:#C9A227;font-size:1.1rem;flex-shrink:0;"></i>
                                <div>{{ session('contact_success') }}</div>
                            </div>
                            @endif

                            @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="{{ route('contact.send') }}" method="POST" novalidate>
                                @csrf

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" value="{{ old('name') }}"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="John Smith" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="email" value="{{ old('email') }}"
                                               class="form-control @error('email') is-invalid @enderror"
                                               placeholder="john@example.com" required>
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                                            Phone Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               placeholder="+1 234 567 8900" required>
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                                            Subject <span class="text-danger">*</span>
                                        </label>
                                        <select name="subject"
                                                class="form-select @error('subject') is-invalid @enderror" required>
                                            <option value="" disabled {{ old('subject') ? '' : 'selected' }}>
                                                Choose a subject…
                                            </option>
                                            @foreach(['Room Reservation','General Enquiry','Restaurant Booking',
                                                      'Conference & Events','Feedback','Other'] as $opt)
                                            <option value="{{ $opt }}" {{ old('subject') == $opt ? 'selected' : '' }}>
                                                {{ $opt }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                                        Message <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="message" rows="6"
                                              class="form-control @error('message') is-invalid @enderror"
                                              placeholder="Tell us how we can help you…"
                                              required>{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn w-100 py-3 fw-semibold"
                                        style="background:#C9A227;color:#fff;border:none;border-radius:6px;
                                               font-size:1rem;letter-spacing:.04em;transition:background .2s,transform .15s;"
                                        onmouseover="this.style.background='#b08c20';this.style.transform='translateY(-1px)'"
                                        onmouseout="this.style.background='#C9A227';this.style.transform='none'">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- Contact Info Sidebar --}}
                <div class="col-lg-5">

                    <div class="card border-0 shadow-sm mb-4"
                         style="border-radius:12px;overflow:hidden;background:#0D1B2A;color:#fff;">
                        <div class="card-body p-4">
                            <h3 style="font-family:'Playfair Display',Georgia,serif;
                                       font-size:1.3rem;margin-bottom:24px;color:#fff;">
                                Contact Information
                            </h3>

                            @php
                                $infoItems = array_filter([
                                    ['icon'=>'bi-geo-alt-fill',   'label'=>'Address',              'value'=>$settings['hotel_address']??null, 'href'=>null],
                                    ['icon'=>'bi-telephone-fill', 'label'=>'Phone',                'value'=>$settings['hotel_phone']??null,   'href'=>'tel:'.preg_replace('/[^\d+]/','', $settings['hotel_phone']??'')],
                                    ['icon'=>'bi-envelope-fill',  'label'=>'Email',                'value'=>$settings['hotel_email']??null,   'href'=>'mailto:'.($settings['hotel_email']??'')],
                                    ['icon'=>'bi-clock-fill',     'label'=>'Check-in / Check-out', 'value'=>\App\Models\Setting::get('check_in_time','15:00').' – '.\App\Models\Setting::get('check_out_time','11:00'), 'href'=>null],
                                ], fn($i) => !empty($i['value']));

                                $socials = array_filter([
                                    ['icon'=>'bi-facebook',  'url'=>\App\Models\Setting::get('facebook_url')],
                                    ['icon'=>'bi-instagram', 'url'=>\App\Models\Setting::get('instagram_url')],
                                    ['icon'=>'bi-twitter-x', 'url'=>\App\Models\Setting::get('twitter_url')],
                                ], fn($s) => !empty($s['url']) && $s['url'] !== '#');
                            @endphp

                            @foreach($infoItems as $item)
                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div style="width:42px;height:42px;border-radius:8px;flex-shrink:0;
                                            background:rgba(201,162,39,.15);
                                            display:flex;align-items:center;justify-content:center;">
                                    <i class="bi {{ $item['icon'] }}" style="color:#C9A227;font-size:1rem;"></i>
                                </div>
                                <div>
                                    <p style="font-size:.72rem;color:rgba(255,255,255,.5);
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
                            @endforeach

                            @if(!empty($socials))
                            <div class="mt-2 pt-3" style="border-top:1px solid rgba(255,255,255,.1);">
                                <p style="font-size:.72rem;color:rgba(255,255,255,.5);
                                          text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;">
                                    Follow Us
                                </p>
                                <div class="d-flex gap-2">
                                    @foreach($socials as $s)
                                    <a href="{{ $s['url'] }}" target="_blank" rel="noopener"
                                       style="width:38px;height:38px;border-radius:8px;
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

                    {{-- Quick links --}}
                    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                        <div class="card-body p-4">
                            <h4 style="font-size:1rem;font-weight:700;color:#0D1B2A;margin-bottom:16px;">
                                Quick Links
                            </h4>
                            <div class="d-flex flex-column gap-2">
                                @foreach([
                                    ['label'=>'Book a Room',        'href'=> route('booking.create'), 'icon'=>'bi-calendar-check'],
                                    ['label'=>'View Our Rooms',     'href'=> route('rooms.index'),    'icon'=>'bi-door-open'],
                                    ['label'=>'Check Availability', 'href'=> route('booking.check'),  'icon'=>'bi-search'],
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

@endif

{{-- ── Map section — always shown below builder or static content ─────────── --}}
@php
    $mapEmbed = \App\Models\Setting::get('map_embed');
    if ($mapEmbed) {
        // Strip width/height HTML attributes so CSS controls dimensions reliably
        $mapEmbed = preg_replace('/\s+width=["\'][^"\']*["\']/i', '', $mapEmbed);
        $mapEmbed = preg_replace('/\s+height=["\'][^"\']*["\']/i', '', $mapEmbed);
        // Inject inline styles directly onto the <iframe> tag
        $mapEmbed = preg_replace(
            '/<iframe/i',
            '<iframe style="width:100%;height:100%;border:none;display:block;"',
            $mapEmbed,
            1
        );
    }
@endphp
@if($mapEmbed)
<div style="height:420px;overflow:hidden;position:relative;">
    {!! $mapEmbed !!}
</div>
@else
<div style="height:280px;background:linear-gradient(135deg,#0D1B2A,#1a3a5c);
            display:flex;align-items:center;justify-content:center;text-align:center;">
    <div style="color:rgba(255,255,255,.35);">
        <i class="bi bi-map" style="font-size:3rem;display:block;margin-bottom:12px;"></i>
        <p style="margin:0;font-size:.85rem;">
            Add a Google Maps embed code in<br>
            <strong style="color:rgba(255,255,255,.5);">Admin → Settings → General → Map Embed</strong>
        </p>
    </div>
</div>
@endif

@endsection
