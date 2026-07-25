@extends('layouts.admin')
@section('page-title', 'Add ' . ($types[$selectedType] ?? 'Event'))
@section('content')
<div class="card">
    <div class="card-header">New {{ $types[$selectedType] ?? 'Event' }}</div>
    <div class="card-body">
        <form action="{{ route('admin.events.store') }}" method="POST">
            @csrf
            @include('admin.events._form')
            <button class="btn text-white" style="background:#C9A227;border:none;">Create</button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
