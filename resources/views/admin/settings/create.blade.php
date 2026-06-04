@extends('layouts.admin')
@section('page-title', 'Settings')
@section('content')
<div class="card">
    <div class="card-body">
        <p class="text-muted"><i class="bi bi-info-circle"></i> Use the <a href="{{ route('admin.settings.index') }}">main settings page</a> to configure hotel settings.</p>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-primary" style="background:#C9A227;border:none;">Go to Settings</a>
    </div>
</div>
@endsection
