@extends('layouts.admin')
@section('page-title', 'New Menu Item')
@section('content')

<div class="card">
    <div class="card-header">New Menu Item</div>
    <div class="card-body">
        <form action="{{ route('admin.restaurant.menu-items.store') }}" method="POST">
            @csrf
            @include('admin.restaurant.menu-items._form')
            <button class="btn text-white" style="background:#C9A227;border:none;">Create Item</button>
            <a href="{{ route('admin.restaurant.menu-items.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
