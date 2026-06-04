@extends('layouts.admin')
@section('page-title', 'Booking Calendar')

@section('content')
<div class="row mb-3 align-items-center">
    <div class="col">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list-ul me-1"></i>List View
        </a>
        <a href="{{ route('admin.bookings.export') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-download me-1"></i>Export CSV
        </a>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.bookings.create') }}" class="btn btn-sm" style="background:#C9A227;color:white;border:none;">
            <i class="bi bi-plus-circle me-1"></i>New Booking
        </a>
    </div>
</div>

<!-- Legend -->
<div class="d-flex flex-wrap gap-3 mb-3">
    <span><span class="badge" style="background:#ffc107; color:#000;">●</span> Pending</span>
    <span><span class="badge" style="background:#17a2b8;">●</span> Confirmed</span>
    <span><span class="badge" style="background:#28a745;">●</span> Checked In</span>
    <span><span class="badge" style="background:#6c757d;">●</span> Checked Out</span>
</div>

<div class="card">
    <div class="card-body p-2">
        <div id="calendar"></div>
    </div>
</div>

<!-- FullCalendar v6 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        aspectRatio: 1.8,
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,listWeek'
        },
        events: @json($events ?? []),
        eventDisplay: 'block',
        dayMaxEvents: 4,
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            } else if (info.event.id) {
                window.location.href = '/admin/bookings/' + info.event.id;
            }
        },
        dateClick: function(info) {
            // Clicking a date opens new booking form with that date pre-filled
            window.location.href = '{{ route("admin.bookings.create") }}?date=' + info.dateStr;
        },
        eventContent: function(arg) {
            return {
                html: '<div style="padding:2px 4px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; font-size:0.8em;">' +
                      arg.event.title + '</div>'
            };
        }
    });
    calendar.render();
});
</script>

<style>
.fc .fc-daygrid-day:hover { background: #fffdf0; cursor: pointer; }
.fc .fc-daygrid-day-frame { min-height: 80px; }
</style>
@endsection
