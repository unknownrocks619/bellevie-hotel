@extends('layouts.admin')
@section('page-title', 'Settings')
@section('content')
<div class="card">
    <div class="card-header">All Settings</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead><tr>
                    <th>Setting</th><th>Value</th>
                </tr></thead>
                <tbody>
                @forelse($settings as $key => $value)
                <tr>
                    <td><strong>{{ str_replace('_', ' ', ucfirst($key)) }}</strong></td>
                    <td>
                        @if(filter_var($value, FILTER_VALIDATE_URL))
                            <a href="{{ $value }}" target="_blank">{{ $value }}</a>
                        @else
                            {{ $value ?? '-' }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-center text-muted py-4">No settings found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<a href="{{ route('admin.settings.index') }}" class="btn btn-primary mt-3" style="background:#C9A227;border:none;">Edit Settings</a>
@endsection
