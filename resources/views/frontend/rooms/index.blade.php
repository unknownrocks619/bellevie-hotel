@extends('layouts.app')
@section('title', 'Rooms & Suites — ' . \App\Models\Setting::get('hotel_name', 'Bellevie Hotel'))
@section('content')

@php
    $currency   = \App\Models\Setting::get('currency_symbol', '$');
    $totalRooms = $rooms->total();
@endphp

{{-- ── Page Hero ──────────────────────────────────────────────────────────────── --}}
<div style="position:relative;min-height:340px;display:flex;align-items:flex-end;
            background:linear-gradient(160deg,#0D1B2A 0%,#1a3a5c 60%,#0d2235 100%);overflow:hidden;">

    {{-- Decorative diagonal pattern --}}
    <div style="position:absolute;inset:0;opacity:.035;pointer-events:none;
                background-image:repeating-linear-gradient(45deg,#C9A227 0,#C9A227 1px,transparent 0,transparent 50%);
                background-size:22px 22px;"></div>

    {{-- Gold top accent --}}
    <div style="position:absolute;top:0;left:0;right:0;height:3px;
                background:linear-gradient(90deg,transparent 0%,#C9A227 40%,#C9A227 60%,transparent 100%);"></div>

    <div class="container" style="position:relative;z-index:2;padding-top:110px;padding-bottom:52px;">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" style="margin-bottom:18px;">
            <ol style="list-style:none;display:flex;align-items:center;gap:6px;padding:0;margin:0;font-size:.8rem;">
                <li>
                    <a href="{{ route('home') }}"
                       style="color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;"
                       onmouseover="this.style.color='#C9A227'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                        <i class="bi bi-house" style="margin-right:4px;"></i>Home
                    </a>
                </li>
                <li style="color:rgba(255,255,255,.3);">›</li>
                <li style="color:rgba(255,255,255,.75);">Rooms &amp; Suites</li>
            </ol>
        </nav>

        {{-- Title + view toggle --}}
        <div class="d-flex flex-column flex-md-row align-items-md-flex-end justify-content-between gap-3">
            <div>
                <p style="color:#C9A227;font-size:.7rem;font-weight:700;letter-spacing:.18em;
                           text-transform:uppercase;margin:0 0 8px;">DISCOVER LUXURY ACCOMMODATIONS</p>
                <h1 style="font-family:'Playfair Display',Georgia,serif;color:#fff;
                            font-size:clamp(1.9rem,4vw,2.9rem);font-weight:700;margin:0 0 6px;line-height:1.2;">
                    Rooms &amp; Suites
                </h1>
                <p style="color:rgba(255,255,255,.5);margin:0;font-size:.88rem;">
                    {{ $totalRooms }} {{ Str::plural('room', $totalRooms) }} available
                </p>
            </div>

            {{-- Grid / List toggle --}}
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                <span style="color:rgba(255,255,255,.4);font-size:.75rem;margin-right:2px;">View as:</span>
                <button id="btnGrid" onclick="setView('grid')" title="Grid view"
                    style="width:40px;height:40px;border-radius:8px;cursor:pointer;
                           display:flex;align-items:center;justify-content:center;transition:all .2s;">
                    <i class="bi bi-grid-3x3-gap-fill" style="font-size:1rem;"></i>
                </button>
                <button id="btnList" onclick="setView('list')" title="List view"
                    style="width:40px;height:40px;border-radius:8px;cursor:pointer;
                           display:flex;align-items:center;justify-content:center;transition:all .2s;">
                    <i class="bi bi-list-ul" style="font-size:1.1rem;"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Rooms grid/list ────────────────────────────────────────────────────────── --}}
<section style="padding:52px 0 80px;background:#f4f5f7;">
    <div class="container">

        @if($rooms->isEmpty())
        <div style="text-align:center;padding:80px 20px;">
            <i class="bi bi-door-closed" style="font-size:3rem;color:#ccc;display:block;margin-bottom:16px;"></i>
            <p style="font-size:1rem;color:#999;">No rooms available at the moment.</p>
            <a href="{{ route('home') }}" style="color:#C9A227;">Return to Home</a>
        </div>
        @else

        <div id="roomsContainer" class="rooms-grid">
            @foreach($rooms as $room)
            @php
                $img   = $room->featuredImageUrl('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=70');
                $price = $currency . number_format($room->price_per_night, 0);
                $type  = $room->roomType->name ?? 'Room';
            @endphp

            <div class="room-item">

                {{-- ── GRID CARD ─────────────────────────────────────────────── --}}
                <div class="gc-wrap">
                    <div class="room-card"
                         onmouseover="this.style.boxShadow='0 10px 36px rgba(0,0,0,.14)';this.style.transform='translateY(-4px)'"
                         onmouseout="this.style.boxShadow='0 2px 14px rgba(0,0,0,.07)';this.style.transform='none'">

                        <div class="gc-img-wrap">
                            <img src="{{ $img }}" alt="{{ $room->name }}" class="gc-img"
                                 onmouseover="this.style.transform='scale(1.06)'"
                                 onmouseout="this.style.transform='scale(1)'">
                            <div class="gc-type-badge">{{ $type }}</div>
                            <div class="gc-img-fade"></div>
                        </div>

                        <div class="gc-body">
                            <h3 class="gc-title">{{ $room->name }}</h3>

                            <div class="gc-stats">
                                @if($room->size_sqft)
                                <span class="stat-chip"><i class="bi bi-rulers"></i>{{ $room->size_sqft }} ft²</span>
                                @endif
                                @if($room->max_adults)
                                <span class="stat-chip"><i class="bi bi-people"></i>{{ $room->max_adults }} Guests</span>
                                @endif
                                @if($room->bed_type)
                                <span class="stat-chip"><i class="bi bi-moon"></i>{{ $room->bed_type }}</span>
                                @endif
                            </div>

                            @if($room->amenities && $room->amenities->count())
                            <div class="gc-amenities">
                                @foreach($room->amenities->take(3) as $am)
                                <span class="amenity-pill">
                                    <i class="bi {{ $am->icon ?? 'bi-check2' }}"></i>{{ $am->name }}
                                </span>
                                @endforeach
                                @if($room->amenities->count() > 3)
                                <span class="amenity-pill muted">+{{ $room->amenities->count() - 3 }}</span>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="gc-footer">
                            <div>
                                @if($room->show_price ?? true)
                                <span class="price-main">{{ $price }}</span>
                                <span class="price-unit">/ night</span>
                                @else
                                <span style="font-size:.8rem;color:#aaa;font-style:italic;">Price on request</span>
                                @endif
                            </div>
                            <div class="gc-btns">
                                <a href="{{ route('rooms.show', $room) }}" class="btn-ghost">Details</a>
                                <a href="{{ route('booking.create', ['room' => $room->id]) }}" class="btn-gold">Book</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── LIST CARD ─────────────────────────────────────────────── --}}
                <div class="lc-wrap">
                    <div class="room-card lc-card"
                         onmouseover="this.style.boxShadow='0 6px 28px rgba(0,0,0,.12)'"
                         onmouseout="this.style.boxShadow='0 2px 14px rgba(0,0,0,.07)'">

                        <div class="lc-img-wrap">
                            <img src="{{ $img }}" alt="{{ $room->name }}" class="lc-img"
                                 onmouseover="this.style.transform='scale(1.04)'"
                                 onmouseout="this.style.transform='scale(1)'">
                            <div class="gc-type-badge">{{ $type }}</div>
                        </div>

                        <div class="lc-body">
                            <div class="lc-top">
                                <h3 class="lc-title">{{ $room->name }}</h3>
                                @if($room->show_price ?? true)
                                <div class="lc-price">
                                    <span class="price-main">{{ $price }}</span>
                                    <span class="price-unit">/ night</span>
                                </div>
                                @else
                                <div class="lc-price">
                                    <span style="font-size:.82rem;color:#aaa;font-style:italic;white-space:nowrap;">Price on request</span>
                                </div>
                                @endif
                            </div>

                            @if($room->description)
                            <p class="lc-desc">{{ Str::limit(strip_tags($room->description), 160) }}</p>
                            @endif

                            <div class="gc-stats" style="margin-bottom:12px;">
                                @if($room->size_sqft)
                                <span class="stat-chip"><i class="bi bi-rulers"></i>{{ $room->size_sqft }} ft²</span>
                                @endif
                                @if($room->max_adults)
                                <span class="stat-chip"><i class="bi bi-people"></i>{{ $room->max_adults }} Guests</span>
                                @endif
                                @if($room->bed_type)
                                <span class="stat-chip"><i class="bi bi-moon"></i>{{ $room->bed_type }}</span>
                                @endif
                            </div>

                            @if($room->amenities && $room->amenities->count())
                            <div class="gc-amenities" style="margin-bottom:18px;">
                                @foreach($room->amenities->take(5) as $am)
                                <span class="amenity-pill">
                                    <i class="bi {{ $am->icon ?? 'bi-check2' }}"></i>{{ $am->name }}
                                </span>
                                @endforeach
                                @if($room->amenities->count() > 5)
                                <span class="amenity-pill muted">+{{ $room->amenities->count() - 5 }}</span>
                                @endif
                            </div>
                            @endif

                            <div class="lc-btns">
                                <a href="{{ route('rooms.show', $room) }}" class="btn-ghost btn-ghost-lg">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                <a href="{{ route('booking.create', ['room' => $room->id]) }}" class="btn-gold btn-gold-lg">
                                    <i class="bi bi-calendar-check"></i> Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- .room-item --}}
            @endforeach
        </div>{{-- #roomsContainer --}}

        @if($rooms->hasPages())
        <div style="display:flex;justify-content:center;margin-top:44px;">
            {{ $rooms->links('pagination::bootstrap-5') }}
        </div>
        @endif

        @endif
    </div>
</section>

{{-- ── Styles ─────────────────────────────────────────────────────────────────── --}}
<style>
/* ════════ Layout containers ════════ */
#roomsContainer.rooms-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
#roomsContainer.rooms-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* ════════ Grid/List card visibility ════════ */
#roomsContainer.rooms-grid  .lc-wrap { display: none; }
#roomsContainer.rooms-grid  .gc-wrap { display: block; }
#roomsContainer.rooms-list  .gc-wrap { display: none; }
#roomsContainer.rooms-list  .lc-wrap { display: block; }

/* ════════ Shared card base ════════ */
.room-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 14px rgba(0,0,0,.07);
    transition: box-shadow .25s, transform .25s;
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* ════════ Grid card ════════ */
.gc-img-wrap {
    position: relative;
    height: 210px;
    overflow: hidden;
}
.gc-img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .45s;
    display: block;
}
.gc-type-badge {
    position: absolute; top: 12px; left: 12px;
    background: rgba(13,27,42,.72);
    backdrop-filter: blur(6px);
    color: #C9A227;
    font-size: .65rem; font-weight: 700;
    letter-spacing: .09em; text-transform: uppercase;
    border-radius: 20px; padding: 4px 11px;
}
.gc-img-fade {
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 70px;
    background: linear-gradient(transparent, rgba(13,27,42,.35));
}
.gc-body { padding: 18px 18px 0; flex: 1; }
.gc-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.1rem; font-weight: 700;
    color: #0D1B2A; margin: 0 0 10px; line-height: 1.3;
}
.gc-stats {
    display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;
}
.stat-chip {
    font-size: .73rem; color: #777;
    display: inline-flex; align-items: center; gap: 4px;
}
.stat-chip .bi { color: #C9A227; font-size: .68rem; }
.gc-amenities {
    display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 14px;
}
.amenity-pill {
    font-size: .66rem;
    background: #f2f2f2; color: #555;
    border-radius: 20px; padding: 3px 9px;
    display: inline-flex; align-items: center; gap: 3px;
}
.amenity-pill .bi { color: #C9A227; font-size: .6rem; }
.amenity-pill.muted { color: #aaa; }
.gc-footer {
    padding: 13px 18px 18px;
    border-top: 1px solid #f0f0f0;
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    margin-top: auto;
}
.price-main { font-size: 1.2rem; font-weight: 700; color: #C9A227; }
.price-unit  { font-size: .7rem; color: #bbb; }
.gc-btns { display: flex; gap: 6px; }

/* ════════ List card ════════ */
.lc-card { flex-direction: row !important; height: auto !important; }
.lc-img-wrap {
    flex-shrink: 0; width: 270px;
    position: relative; overflow: hidden;
}
.lc-img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .4s; display: block;
}
.lc-body {
    flex: 1; padding: 22px 26px;
    display: flex; flex-direction: column; justify-content: space-between;
    min-width: 0;
}
.lc-top {
    display: flex; justify-content: space-between;
    align-items: flex-start; gap: 12px; margin-bottom: 10px;
}
.lc-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.22rem; font-weight: 700;
    color: #0D1B2A; margin: 0; line-height: 1.3;
}
.lc-price { text-align: right; flex-shrink: 0; }
.lc-price .price-main { font-size: 1.35rem; }
.lc-desc {
    color: #777; font-size: .87rem; line-height: 1.65; margin: 0 0 12px;
}
.lc-btns { display: flex; gap: 8px; margin-top: 6px; }

/* ════════ Buttons ════════ */
.btn-ghost, .btn-ghost-lg {
    border: 1px solid #ddd;
    border-radius: 8px; color: #444;
    text-decoration: none; font-weight: 500;
    transition: border-color .18s, color .18s;
    white-space: nowrap;
    padding: 7px 15px; font-size: .78rem;
    display: inline-flex; align-items: center; gap: 5px;
}
.btn-ghost-lg { padding: 9px 20px; font-size: .84rem; }
.btn-ghost:hover, .btn-ghost-lg:hover {
    border-color: #C9A227; color: #C9A227;
}
.btn-gold, .btn-gold-lg {
    border: none; border-radius: 8px;
    background: #C9A227; color: #fff;
    text-decoration: none; font-weight: 600;
    transition: background .18s;
    white-space: nowrap;
    padding: 7px 16px; font-size: .78rem;
    display: inline-flex; align-items: center; gap: 5px;
}
.btn-gold-lg { padding: 9px 22px; font-size: .84rem; }
.btn-gold:hover, .btn-gold-lg:hover { background: #b08c20; color: #fff; }

/* ════════ Toggle buttons ════════ */
#btnGrid, #btnList {
    border: none; background: none; padding: 0;
}
.toggle-active {
    background: rgba(201,162,39,.22) !important;
    border: 1px solid #C9A227 !important;
    color: #C9A227 !important;
}
.toggle-inactive {
    background: rgba(255,255,255,.07) !important;
    border: 1px solid rgba(255,255,255,.2) !important;
    color: rgba(255,255,255,.45) !important;
}

/* ════════ Responsive ════════ */
@media (max-width: 991px) {
    #roomsContainer.rooms-grid { grid-template-columns: repeat(2, 1fr); gap: 18px; }
    .lc-img-wrap { width: 220px; }
}
@media (max-width: 767px) {
    .lc-card { flex-direction: column !important; }
    .lc-img-wrap { width: 100% !important; height: 220px; }
}
@media (max-width: 575px) {
    #roomsContainer.rooms-grid { grid-template-columns: 1fr; gap: 14px; }
}

/* ════════ Pagination ════════ */
.pagination .page-link { color: #C9A227; }
.pagination .page-item.active .page-link { background: #C9A227; border-color: #C9A227; color: #fff; }
.pagination .page-link:hover { color: #b08c20; }
</style>

{{-- ── View toggle script ──────────────────────────────────────────────────────── --}}
<script>
(function () {
    const container = document.getElementById('roomsContainer');
    const btnGrid   = document.getElementById('btnGrid');
    const btnList   = document.getElementById('btnList');
    if (!container || !btnGrid || !btnList) return;

    function setView(mode) {
        if (mode === 'list') {
            container.className = 'rooms-list';
            btnList.className = 'toggle-active';
            btnGrid.className = 'toggle-inactive';
        } else {
            container.className = 'rooms-grid';
            btnGrid.className = 'toggle-active';
            btnList.className = 'toggle-inactive';
        }
        try { localStorage.setItem('bellevie_rooms_view', mode); } catch (e) {}
    }

    const saved = (function () { try { return localStorage.getItem('bellevie_rooms_view'); } catch (e) { return null; } })();
    setView(saved === 'list' ? 'list' : 'grid');

    window.setView = setView;
})();
</script>

@endsection
