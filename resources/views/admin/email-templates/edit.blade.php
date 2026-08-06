@extends('layouts.admin')
@section('page-title', 'Edit Email Template — ' . $template->name)
@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-envelope-paper me-2"></i>{{ $template->name }}</div>
            <div class="card-body">
                <form action="{{ route('admin.email-templates.update', $template) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" id="tpl-subject" class="form-control"
                               value="{{ old('subject', $template->subject) }}" required>
                        @error('subject') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Body</label>
                        <textarea name="body" id="tpl-body" rows="14" class="form-control" required>{{ old('body', $template->body) }}</textarea>
                        <div class="form-text">
                            This is the message text only — the header, footer and styling of the email are fixed.
                            Use the shortcodes below and click one to insert it at your cursor.
                        </div>
                        @error('body') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn text-white" style="background:#C9A227;">
                        <i class="bi bi-check-circle me-1"></i>Save Template
                    </button>
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-code-slash me-2"></i>Available Shortcodes</div>
            <div class="card-body">
                <p class="text-muted small">Click a shortcode to insert it into the field you last focused (subject or body).</p>
                <div class="d-flex flex-column gap-2">
                    @foreach($shortcodes as $code => $description)
                    <button type="button" class="btn btn-sm btn-outline-secondary text-start shortcode-btn" data-code="[{{ $code }}]">
                        <code>[{{ $code }}]</code>
                        <span class="text-muted d-block small">{{ $description }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var subjectField = document.getElementById('tpl-subject');
    var bodyField = document.getElementById('tpl-body');
    var lastFocused = bodyField;

    [subjectField, bodyField].forEach(function (field) {
        field.addEventListener('focus', function () { lastFocused = field; });
    });

    document.querySelectorAll('.shortcode-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var code = btn.getAttribute('data-code');
            var field = lastFocused || bodyField;
            var start = field.selectionStart ?? field.value.length;
            var end = field.selectionEnd ?? field.value.length;
            field.value = field.value.slice(0, start) + code + field.value.slice(end);
            field.focus();
            var pos = start + code.length;
            field.setSelectionRange(pos, pos);
        });
    });
})();
</script>

@endsection
