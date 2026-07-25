@extends('layouts.admin')
@section('page-title', 'New Menu Category')
@section('content')

<div class="card">
    <div class="card-header">New Menu Category</div>
    <div class="card-body">
        <form action="{{ route('admin.restaurant.categories.store') }}" method="POST">
            @csrf
            @include('admin.restaurant.categories._form')
            <button class="btn text-white" style="background:#C9A227;border:none;">Create Category</button>
            <a href="{{ route('admin.restaurant.categories.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
