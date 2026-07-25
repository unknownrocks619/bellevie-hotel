@extends('layouts.admin')
@section('page-title', 'Edit Menu Item')
@section('content')

<div class="card">
    <div class="card-header">Edit Item: {{ $menuItem->name }}</div>
    <div class="card-body">
        <form action="{{ route('admin.restaurant.menu-items.update', $menuItem) }}" method="POST">
            @csrf @method('PUT')
            @include('admin.restaurant.menu-items._form')
            <button class="btn text-white" style="background:#C9A227;border:none;">Save Changes</button>
            <a href="{{ route('admin.restaurant.menu-items.index') }}" class="btn btn-secondary">Back to List</a>
        </form>
    </div>
</div>
@endsection
