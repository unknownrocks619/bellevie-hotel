<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') - Bellevie Hotel Admin</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        :root {
            --admin-gold: {{ \App\Models\Setting::get('primary_color', '#C9A227') }};
            --admin-dark: #0D1B2A;
            --sidebar-width: 260px;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Playfair Display', serif;
        }

        body {
            background-color: #f5f5f5;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--admin-dark);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            padding: 2rem 1.5rem;
            z-index: 999;
        }

        .sidebar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .sidebar-brand span {
            color: var(--admin-gold);
        }

        .sidebar-subtitle {
            font-size: 0.75rem;
            color: #999;
            margin-bottom: 2rem;
        }

        .nav-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #666;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .nav-link {
            color: #ccc;
            text-decoration: none;
            padding: 0.6rem 0;
            display: flex;
            align-items: center;
            border-left: 3px solid transparent;
            padding-left: 0.75rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: white;
            border-left-color: var(--admin-gold);
        }

        .nav-link.active {
            color: var(--admin-gold);
            border-left-color: var(--admin-gold);
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 1.2rem;
        }

        .badge-pending {
            background-color: #ffc107;
            color: black;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 64px;
            background-color: white;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            z-index: 500;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 64px;
            padding: 2rem;
            min-height: calc(100vh - 64px);
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #e0e0e0;
            padding: 1.5rem;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--admin-gold);
            border-color: var(--admin-gold);
        }

        .btn-primary:hover {
            background-color: #b8911d;
            border-color: #b8911d;
        }

        .table thead th {
            background-color: #f8f8f8;
            border-bottom: 1px solid #ddd;
            color: #333;
            font-weight: 600;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2rem;
            color: var(--admin-gold);
            margin: 0;
        }

        .stat-card p {
            color: #666;
            margin: 0.5rem 0 0 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
                transition: width 0.3s ease;
            }

            .sidebar.show {
                width: var(--sidebar-width);
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">Bellevie<span>.</span></div>
        <div class="sidebar-subtitle">Hotel Management System</div>

        <div class="nav-section-title">Main</div>
        <a href="{{ route('admin.dashboard') }}"
            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section-title">Rooms</div>
        <a href="{{ route('admin.rooms.index') }}"
            class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
            <i class="bi bi-door-closed"></i> Rooms
        </a>
        <a href="{{ route('admin.room-types.index') }}"
            class="nav-link {{ request()->routeIs('admin.room-types.*') ? 'active' : '' }}">
            <i class="bi bi-tag"></i> Room Types
        </a>
        <a href="{{ route('admin.amenities.index') }}"
            class="nav-link {{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}">
            <i class="bi bi-stars"></i> Amenities
        </a>
        <a href="{{ route('admin.pricing.index') }}"
            class="nav-link {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i> Pricing Optimizer
        </a>

        <div class="nav-section-title">Reservations</div>
        <a href="{{ route('admin.bookings.index') }}"
            class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> All Bookings
            @php
                $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
            @endphp
            @if ($pendingCount > 0)
                <span class="badge badge-pending ms-auto">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.bookings.calendar') }}"
            class="nav-link {{ request()->routeIs('admin.bookings.calendar') ? 'active' : '' }}">
            <i class="bi bi-calendar-month"></i> Calendar
        </a>

        <div class="nav-section-title">CRM</div>
        <a href="{{ route('admin.guests.index') }}"
            class="nav-link {{ request()->routeIs('admin.guests.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Guests
        </a>

        <div class="nav-section-title">Content</div>
        <a href="{{ route('admin.blog.index') }}"
            class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
            <i class="bi bi-file-text"></i> Blog
        </a>
        <a href="{{ route('admin.blog-categories.index') }}"
            class="nav-link {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Blog Categories
        </a>
        <a href="{{ route('admin.pages.index') }}"
            class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark"></i> Pages
        </a>
        <a href="{{ route('admin.gallery.index') }}"
            class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
            <i class="bi bi-image"></i> Gallery
        </a>
        <a href="{{ route('admin.testimonials.index') }}"
            class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
            <i class="bi bi-chat-quote"></i> Testimonials
        </a>
        <a href="{{ route('admin.faqs.index') }}"
            class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
            <i class="bi bi-question-circle"></i> FAQ
        </a>
        <a href="{{ route('admin.events.index') }}"
            class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event"></i> Events &amp; Conferences
        </a>
        <a href="{{ route('admin.restaurant.edit') }}"
            class="nav-link {{ request()->routeIs('admin.restaurant.*') ? 'active' : '' }}">
            <i class="bi bi-egg-fried"></i> Restaurant
        </a>
        <a href="{{ route('admin.conference.edit') }}"
            class="nav-link {{ request()->routeIs('admin.conference.*') || request()->routeIs('admin.conference-inquiries.*') ? 'active' : '' }}">
            <i class="bi bi-easel"></i> Conference Hall
        </a>
        <a href="{{ route('admin.menus.index') }}"
            class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
            <i class="bi bi-list"></i> Menus
        </a>

        <div class="nav-section-title">System</div>
        <a href="{{ route('admin.builder.editHome') }}"
            class="nav-link {{ request()->is('admin/home/builder') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Page Builder
        </a>
        <a href="{{ route('admin.builder.editContact') }}"
            class="nav-link {{ request()->is('admin/contact/builder') ? 'active' : '' }}">
            <i class="bi bi-envelope-paper"></i> Contact Page
        </a>
        <a href="{{ route('admin.email-templates.index') }}"
            class="nav-link {{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}">
            <i class="bi bi-envelope-paper"></i> Email Templates
        </a>
        <a href="{{ route('admin.settings.index') }}"
            class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Settings
        </a>
        <a href="{{ route('home') }}" class="nav-link" target="_blank">
            <i class="bi bi-globe"></i> View Website
        </a>

        <hr style="margin-top: 2rem; border-color: #333;">

        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #333;">
            <p style="font-size: 0.85rem; color: #999; margin: 0 0 0.5rem 0;">Logged in as</p>
            <p style="color: white; margin: 0 0 1rem 0; font-weight: 600;">{{ Auth::user()->name }}</p>
            <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Logout</button>
            </form>
        </div>
    </div>

    <div class="topbar">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
        <h5 class="ms-3 mb-0">@yield('page-title', 'Dashboard')</h5>
    </div>

    <div class="main-content">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $('#toggleSidebar').click(function() {
            $('#sidebar').toggleClass('show');
        });

        flatpickr(".datepicker", {
            dateFormat: "Y-m-d"
        });

        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                pageLength: 15
            });
        });
    </script>

    {{-- Image picker modal and shared JS — always rendered at body level --}}
    @stack('modals')
    @stack('page_script')
</body>

</html>
