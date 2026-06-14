<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', \App\Models\Setting::get('hotel_name', 'Bellevie Hotel'))</title>

    {{-- SEO Meta Tags --}}
    @php
        $seoTitle =
            View::yieldContent('seo_title') ?:
            $seoTitle ?? null ?:
            \App\Models\Setting::get('hotel_name', 'Bellevie Hotel');
        $seoDescription =
            View::yieldContent('seo_description') ?:
            $seoDescription ?? null ?:
            \App\Models\Setting::get('hotel_description', '');
        $seoImage = View::yieldContent('seo_image') ?: $seoImage ?? null ?: '';
    @endphp
    <meta name="description" content="{{ $seoDescription }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    @if ($seoImage)
        <meta property="og:image" content="{{ $seoImage }}">
    @endif
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if ($seoImage)
        <meta name="twitter:image" content="{{ $seoImage }}">
    @endif
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    {{-- <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @php
        $primaryColor = \App\Models\Setting::get('primary_color', '#C9A227');
        $primaryColorDark =
            '#' .
            implode(
                '',
                array_map(
                    fn($c) => str_pad(dechex(max(0, hexdec($c) - 20)), 2, '0', STR_PAD_LEFT),
                    str_split(ltrim($primaryColor, '#'), 2),
                ),
            );
        $hotelName = \App\Models\Setting::get('hotel_name', 'Bellevie Hotel');
        $logoType = \App\Models\Setting::get('site_logo_type', 'text');
        $logoUrl = \App\Models\Setting::get('logo_url', '');
    @endphp

    <style>
        :root {
            --gold: {{ $primaryColor }};
            --gold-dark: {{ $primaryColorDark }};
            --dark: #0D1B2A;
            --cream: #F5F0E8;
        }

        * {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 700;
        }

        body {
            background-color: #f9f8f6;
        }

        .navbar {
            background-color: transparent;
            padding: 1.5rem 0;
            transition: all 0.3s ease;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid transparent;
        }

        .navbar.scrolled {
            background-color: rgba(13, 27, 42, 0.95);
            border-bottom-color: var(--gold);
            padding: 0.5rem 0;
        }

        .navbar-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: white !important;
        }

        .navbar-brand span {
            color: var(--gold);
        }

        .nav-link {
            color: white !important;
            margin: 0 1rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--gold) !important;
        }

        .btn-book-now {
            background-color: var(--gold);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 4px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-book-now:hover {
            background-color: var(--gold-dark);
            color: white;
        }

        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1551632786-de41ec28eef7?auto=format&fit=crop&w=1200&q=80') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            50% {
                transform: translateX(-50%) translateY(-10px);
            }
        }

        footer {
            background-color: var(--dark);
            color: white;
            padding: 3rem 0;
            margin-top: 5rem;
        }

        footer a {
            color: var(--gold);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.5rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .nav-link {
                margin: 0.5rem 0;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if ($logoType === 'image' && $logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $hotelName }}" style="max-height: 48px; width: auto;">
                @else
                    @php $parts = explode(' ', $hotelName, 2); @endphp
                    {{ $parts[0] }}<span>{{ $parts[1] ?? '' }}</span>
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list" style="color: white;"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @php
                        $headerMenu = \App\Models\Menu::where('location', 'header')->with('items')->first();
                    @endphp
                    @if ($headerMenu)
                        @foreach ($headerMenu->items as $item)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $item->menuLink() }}">{{ $item->title }}</a>
                            </li>
                        @endforeach
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('booking.create') }}" class="btn-book-now ms-2">Book Now</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @foreach ($headerMenu->items as $item)
        {{-- @dd($item) --}}
        {{-- @dump($item->menuLink()) --}}
    @endforeach

    @yield('content')

    <footer>
        <div class="container">
            @php
                $footerHotelName = \App\Models\Setting::get('hotel_name', 'Bellevie Hotel');
                $footerTagline = \App\Models\Setting::get('hotel_tagline', 'Where Luxury Meets Serenity');
                $footerAddress = \App\Models\Setting::get('hotel_address', '100 Grand Boulevard');
                $footerCity = \App\Models\Setting::get('hotel_city', 'Beverly Hills');
                $footerCountry = \App\Models\Setting::get('hotel_country', 'USA');
                $footerPhone = \App\Models\Setting::get('hotel_phone', '+1 (310) 555-0100');
                $footerEmail = \App\Models\Setting::get('hotel_email', 'info@belleviehotel.com');
                $footerCheckIn = \App\Models\Setting::get('check_in_time', '15:00');
                $footerCheckOut = \App\Models\Setting::get('check_out_time', '11:00');
                $footerFacebook = \App\Models\Setting::get('facebook_url', '#');
                $footerInstagram = \App\Models\Setting::get('instagram_url', '#');
                $footerTwitter = \App\Models\Setting::get('twitter_url', '#');

                // Load active footer pages
                $footerPages = \App\Models\Page::where('is_active', true)->orderBy('sort_order')->get();
            @endphp

            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <h5 style="color: var(--gold);">{{ $footerHotelName }}</h5>
                    <p class="text-white-50">{{ $footerTagline }}</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3"
                        style="color: var(--gold); font-size: 0.8rem; letter-spacing: 1px;">Contact</h6>
                    <p class="text-white-50 small mb-1">
                        <i class="bi bi-geo-alt me-2"></i>{{ $footerAddress }}, {{ $footerCity }},
                        {{ $footerCountry }}
                    </p>
                    <p class="text-white-50 small mb-1">
                        <i class="bi bi-telephone me-2"></i><a href="tel:{{ $footerPhone }}"
                            style="color: inherit; text-decoration: none;">{{ $footerPhone }}</a>
                    </p>
                    <p class="text-white-50 small mb-1">
                        <i class="bi bi-envelope me-2"></i><a href="mailto:{{ $footerEmail }}"
                            style="color: inherit; text-decoration: none;">{{ $footerEmail }}</a>
                    </p>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3"
                        style="color: var(--gold); font-size: 0.8rem; letter-spacing: 1px;">Hours</h6>
                    <p class="text-white-50 small mb-1">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Check-in:
                        {{ \Carbon\Carbon::createFromFormat('H:i', $footerCheckIn)->format('g:i A') }}
                    </p>
                    <p class="text-white-50 small mb-1">
                        <i class="bi bi-box-arrow-right me-2"></i>Check-out:
                        {{ \Carbon\Carbon::createFromFormat('H:i', $footerCheckOut)->format('g:i A') }}
                    </p>
                    <p class="text-white-50 small">
                        <i class="bi bi-clock me-2"></i>24/7 Front Desk
                    </p>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3"
                        style="color: var(--gold); font-size: 0.8rem; letter-spacing: 1px;">Follow Us</h6>
                    <div class="d-flex gap-3 fs-5">
                        @if ($footerFacebook && $footerFacebook !== '#')
                            <a href="{{ $footerFacebook }}" target="_blank" rel="noopener" class="text-white-50"><i
                                    class="bi bi-facebook"></i></a>
                        @endif
                        @if ($footerInstagram && $footerInstagram !== '#')
                            <a href="{{ $footerInstagram }}" target="_blank" rel="noopener" class="text-white-50"><i
                                    class="bi bi-instagram"></i></a>
                        @endif
                        @if ($footerTwitter && $footerTwitter !== '#')
                            <a href="{{ $footerTwitter }}" target="_blank" rel="noopener" class="text-white-50"><i
                                    class="bi bi-twitter-x"></i></a>
                        @endif
                    </div>

                    @if ($footerPages->count() > 0)
                        <div class="mt-4">
                            <h6 class="text-uppercase fw-bold mb-2"
                                style="color: var(--gold); font-size: 0.8rem; letter-spacing: 1px;">Information</h6>
                            @foreach ($footerPages as $fPage)
                                <div class="mb-1">
                                    <a href="{{ route('page.show', $fPage->slug) }}" class="text-white-50 small"
                                        style="text-decoration: none;">
                                        <i class="bi bi-chevron-right me-1"
                                            style="font-size: 0.65rem;"></i>{{ $fPage->title }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="row pt-3 border-top border-secondary">
                <div class="col-md-8">
                    <small class="text-white-50">&copy; {{ date('Y') }} {{ $footerHotelName }}. All rights
                        reserved.</small>
                </div>
                <div class="col-md-4 text-md-end">
                    <small class="text-white-50">Powered by <span style="color: var(--gold);">Bellevie
                            CMS</span></small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(window).scroll(function() {
            if ($(window).scrollTop() > 50) {
                $('nav').addClass('scrolled');
            } else {
                $('nav').removeClass('scrolled');
            }
        });

        flatpickr(".datepicker", {
            minDate: "today",
            dateFormat: "Y-m-d"
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>

</html>
