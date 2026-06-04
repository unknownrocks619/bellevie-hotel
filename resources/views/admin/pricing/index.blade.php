@extends('layouts.admin')
@section('page-title', 'Pricing Optimizer')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="mb-1">Pricing Optimizer</h4>
        <p class="text-muted mb-0">AI-powered rate recommendations based on occupancy, demand &amp; competition.</p>
    </div>
    <form method="GET" class="d-flex gap-2 align-items-center">
        <small class="text-muted">Last refreshed: {{ now()->format('M d, g:i A') }}</small>
        <button type="submit" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
        </button>
    </form>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Summary Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#f0f7ff;">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold" style="color:#3b5bdb;">{{ $stats['total_rooms'] }}</div>
                <div class="small text-muted">Total Rooms</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#f0fff4;">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-success">{{ $stats['rooms_to_raise'] }}</div>
                <div class="small text-muted">Raise Price</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#fff5f5;">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-danger">{{ $stats['rooms_to_lower'] }}</div>
                <div class="small text-muted">Lower Price</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#fffbf0;">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold" style="color:#C9A227;">{{ number_format($stats['avg_occupancy'], 1) }}%</div>
                <div class="small text-muted">Avg Occupancy</div>
            </div>
        </div>
    </div>
</div>

{{-- Monitoring rooms notice --}}
@if($stats['rooms_monitoring'] > 0)
<div class="alert d-flex align-items-center gap-3 mb-4" style="background:#f0f4ff; border:1px solid #c5d0fa; border-radius:12px;">
    <i class="bi bi-hourglass-split fs-4" style="color:#3b5bdb; flex-shrink:0;"></i>
    <div>
        <strong style="color:#1a1a6c;">{{ $stats['rooms_monitoring'] }} room{{ $stats['rooms_monitoring'] > 1 ? 's' : '' }} in monitoring period</strong>
        <p class="mb-0 small text-muted">After a price change, the system waits 14 days to measure impact before making new recommendations. This prevents over-correcting.</p>
    </div>
</div>
@endif

{{-- Room Cards --}}
<div class="row g-4">
    @foreach($recommendations as $rec)
    @php
        $room   = $rec['room'];
        $action = $rec['action'];

        if ($action === 'monitoring') {
            // Monitoring state handled separately
        } else {
            $change  = $rec['change_percent'];
            $isUp    = $change > 0;
            $isDown  = $change < 0;
            $isSame  = $change == 0;

            $changeBg    = $isUp   ? '#f0fff4' : ($isDown ? '#fff5f5' : '#f8f9fa');
            $changeColor = $isUp   ? '#2f9e44' : ($isDown ? '#e03131' : '#868e96');
            $changeArrow = $isUp   ? 'bi-graph-up-arrow' : ($isDown ? 'bi-graph-down-arrow' : 'bi-dash-lg');
            $occupancyColor = $rec['occupancy_rate'] >= 60 ? '#2f9e44' : ($rec['occupancy_rate'] >= 30 ? '#f08c00' : '#e03131');
        }
    @endphp

    <div class="col-md-6 col-xl-6">

        @if($action === 'monitoring')
        {{-- ===== MONITORING CARD ===== --}}
        @php
            $cd = $rec['cooldown'];
            $progressPct = min(100, round(($cd['days_elapsed'] / $cd['total_days']) * 100));
            $direction = $cd['new_price'] > $cd['old_price'] ? 'raised' : 'lowered';
            $dirColor  = $direction === 'raised' ? '#2f9e44' : '#e03131';
            $dirIcon   = $direction === 'raised' ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow';
        @endphp
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px; overflow:hidden; border-left:4px solid #3b5bdb !important; border-left-style:solid;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:#1a1a2e; font-family:'Playfair Display',serif;">
                            {{ $room->name }}
                        </h5>
                        <span class="badge" style="background:#e8edff; color:#3b5bdb; font-size:0.72rem; border-radius:8px;">
                            <i class="bi bi-hourglass-split me-1"></i>Monitoring Performance
                        </span>
                    </div>
                    <div style="width:44px; height:44px; border-radius:10px; background:rgba(59,91,219,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="bi bi-activity" style="color:#3b5bdb; font-size:1.3rem;"></i>
                    </div>
                </div>

                {{-- Price change summary --}}
                <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-3" style="background:#f8f9fa;">
                    <div class="text-center" style="flex:1;">
                        <div class="small text-muted mb-1">Previous Rate</div>
                        <div class="fs-5 fw-bold text-muted">${{ number_format($cd['old_price'], 0) }}</div>
                    </div>
                    <div class="text-center" style="color:{{ $dirColor }};">
                        <i class="bi {{ $dirIcon }} fs-4"></i>
                        <div class="small fw-semibold">{{ $direction === 'raised' ? '+' : '' }}{{ number_format((($cd['new_price'] - $cd['old_price']) / $cd['old_price']) * 100, 1) }}%</div>
                    </div>
                    <div class="text-center" style="flex:1;">
                        <div class="small text-muted mb-1">New Rate</div>
                        <div class="fs-5 fw-bold" style="color:#1a1a2e;">${{ number_format($cd['new_price'], 0) }}</div>
                    </div>
                </div>

                {{-- Monitoring progress --}}
                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Monitoring period</small>
                        <small class="fw-semibold" style="color:#3b5bdb;">{{ $cd['days_left'] }} day{{ $cd['days_left'] != 1 ? 's' : '' }} remaining</small>
                    </div>
                    <div style="height:8px; background:#e9ecef; border-radius:4px; overflow:hidden;">
                        <div style="height:100%; width:{{ $progressPct }}%; background:linear-gradient(90deg,#3b5bdb,#748ffc); border-radius:4px; transition:width 0.6s;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">Day {{ $cd['days_elapsed'] }}</small>
                        <small class="text-muted">Day {{ $cd['total_days'] }}</small>
                    </div>
                </div>

                <p class="text-muted small mb-0 mt-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Price {{ $direction }} on {{ \Carbon\Carbon::parse($cd['applied_at'])->format('M d, Y') }}.
                    New recommendations will appear on {{ \Carbon\Carbon::parse($cd['applied_at'])->addDays($cd['total_days'])->format('M d, Y') }}.
                </p>
            </div>

            {{-- Reset / Clear actions --}}
            <div class="card-footer border-0 px-3 pb-3 pt-0">
                <div class="d-flex gap-2">
                    {{-- Reset to original price --}}
                    <form action="{{ route('admin.pricing.reset', $room) }}" method="POST" class="flex-fill">
                        @csrf
                        <button type="submit"
                                class="btn w-100 fw-semibold"
                                style="background:#fff3cd; color:#856404; border:1px solid #ffc107; border-radius:10px; font-size:0.82rem;"
                                onclick="return confirm('Reset {{ addslashes($room->name) }} price back to original ${{ number_format($cd['old_price'], 0) }}?\n\nThe monitoring period will also be cleared.')">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset to ${{ number_format($cd['old_price'], 0) }}
                        </button>
                    </form>

                    {{-- Clear monitoring period only --}}
                    <form action="{{ route('admin.pricing.clear-cooldown', $room) }}" method="POST" class="flex-fill">
                        @csrf
                        <button type="submit"
                                class="btn w-100 fw-semibold"
                                style="background:#e9ecef; color:#495057; border:1px solid #dee2e6; border-radius:10px; font-size:0.82rem;"
                                onclick="return confirm('Clear the monitoring period for {{ addslashes($room->name) }}?\n\nThe current price (${{ number_format($cd['new_price'], 0) }}) will be kept and fresh recommendations will be generated immediately.')">
                            <i class="bi bi-skip-forward me-1"></i>
                            Analyse Now
                        </button>
                    </form>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size:0.73rem; text-align:center;">
                    Reset restores the original price &amp; clears history &nbsp;·&nbsp; Analyse Now keeps the new price and generates a fresh recommendation
                </p>
            </div>
        </div>

        @else
        {{-- ===== RECOMMENDATION CARD ===== --}}
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px; overflow:hidden;">
            <div class="card-body pb-0">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:#1a1a2e; font-family:'Playfair Display',serif;">
                            {{ $room->name }}
                        </h5>
                        <p class="text-muted small mb-0">{{ $rec['reason'] }}</p>
                    </div>
                    <div style="width:44px; height:44px; border-radius:10px; background:rgba(201,162,39,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="bi bi-currency-dollar" style="color:#C9A227; font-size:1.3rem;"></i>
                    </div>
                </div>

                {{-- Occupancy bar --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">30-day occupancy</small>
                        <small class="fw-semibold" style="color:{{ $occupancyColor }}">{{ $rec['occupancy_rate'] }}%</small>
                    </div>
                    <div style="height:6px; background:#eee; border-radius:3px; overflow:hidden;">
                        <div style="height:100%; width:{{ min($rec['occupancy_rate'], 100) }}%; background:{{ $occupancyColor }}; border-radius:3px; transition:width 0.6s;"></div>
                    </div>
                </div>

                {{-- Rates Row --}}
                <div class="row g-0 mb-3">
                    <div class="col-6 pe-3">
                        <div class="small text-muted mb-1">Current Rate</div>
                        <div class="fs-4 fw-bold" style="color:#1a1a2e;">${{ number_format($rec['current_price'], 0) }}</div>
                    </div>
                    <div class="col-6 ps-3" style="border-left:1px solid #eee;">
                        <div class="small text-muted mb-1">Recommended Rate</div>
                        <div class="fs-4 fw-bold" style="color:#3b5bdb;">
                            ${{ number_format($rec['recommended_price'], 0) }}
                        </div>
                    </div>
                </div>

                {{-- Price Change Badge --}}
                <div class="d-flex justify-content-between align-items-center mb-3 px-3 py-2 rounded-3" style="background:{{ $changeBg }};">
                    <span class="small fw-semibold text-muted">Price Change</span>
                    <span class="fw-bold d-flex align-items-center gap-1" style="color:{{ $changeColor }};">
                        <i class="bi {{ $changeArrow }}" style="font-size:0.9rem;"></i>
                        {{ $change > 0 ? '+' : '' }}{{ $change }}%
                    </span>
                </div>

                {{-- Stats row --}}
                <div class="row g-2 mb-3 text-center">
                    <div class="col-4">
                        <div class="small text-muted">Bookings (30d)</div>
                        <div class="fw-semibold">{{ $rec['upcoming_bookings'] }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Next 7 Days</div>
                        <div class="fw-semibold">{{ $rec['urgent_bookings'] }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Cancel Rate</div>
                        <div class="fw-semibold {{ $rec['cancellation_rate'] > 20 ? 'text-danger' : '' }}">
                            {{ $rec['cancellation_rate'] }}%
                        </div>
                    </div>
                </div>
            </div>

            {{-- Apply Button --}}
            @if($isSame)
            <div class="card-footer border-0 p-3 pt-0">
                <button class="btn w-100 py-2 fw-semibold" style="background:#f1f3f5; color:#868e96; border-radius:10px; border:none; cursor:default;">
                    <i class="bi bi-check-circle me-2"></i>Rate is Optimal
                </button>
            </div>
            @else
            <div class="card-footer border-0 p-3 pt-0">
                <form action="{{ route('admin.pricing.apply', $room) }}" method="POST">
                    @csrf
                    <input type="hidden" name="new_price" value="{{ $rec['recommended_price'] }}">
                    <button type="submit"
                            class="btn w-100 py-2 fw-semibold"
                            style="background:#1a1a6c; color:white; border-radius:10px; border:none;"
                            onclick="return confirm('Update {{ addslashes($room->name) }} price from ${{ number_format($rec['current_price'],0) }} to ${{ number_format($rec['recommended_price'],0) }}?\n\nThe system will then monitor performance for 14 days before making new recommendations.')">
                        Apply Recommendation
                    </button>
                </form>
                @if($isDown)
                <div class="text-center mt-2">
                    <form action="{{ route('admin.pricing.apply', $room) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="new_price" value="{{ round($rec['current_price'] * 0.95, 2) }}">
                        <button type="submit" class="btn btn-link btn-sm text-muted p-0" style="font-size:0.78rem;">
                            Apply smaller 5% discount instead
                        </button>
                    </form>
                </div>
                @elseif($isUp)
                <div class="text-center mt-2">
                    <form action="{{ route('admin.pricing.apply', $room) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="new_price" value="{{ round($rec['current_price'] * 1.05, 2) }}">
                        <button type="submit" class="btn btn-link btn-sm text-muted p-0" style="font-size:0.78rem;">
                            Apply smaller 5% increase instead
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

    </div>
    @endforeach
</div>

{{-- Legend --}}
<div class="card border-0 mt-4" style="background:#f8f9fa;">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap gap-4 align-items-center">
            <small class="fw-semibold text-muted">How it works:</small>
            <span class="small"><i class="bi bi-graph-up-arrow text-success me-1"></i><strong>Raise</strong> — occupancy &gt; 60%</span>
            <span class="small"><i class="bi bi-dash-lg text-secondary me-1"></i><strong>Maintain</strong> — occupancy 35–60%</span>
            <span class="small"><i class="bi bi-graph-down-arrow text-danger me-1"></i><strong>Lower</strong> — occupancy &lt; 35%</span>
            <span class="small"><i class="bi bi-hourglass-split me-1" style="color:#3b5bdb;"></i><strong>Monitoring</strong> — 14-day wait after price change</span>
        </div>
    </div>
</div>

@endsection
