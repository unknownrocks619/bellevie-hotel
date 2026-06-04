@extends('layouts.admin')
@section('page-title', 'Menu Item Details')
@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $item->title }}</span>
                <div>
                    <a href="{{ route('admin.menus.edit',$item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.menus.destroy',$item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this item?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td><strong>Title:</strong></td><td>{{ $item->title }}</td></tr>
                    <tr><td><strong>Menu:</strong></td><td>{{ ucfirst($item->menu_id) }}</td></tr>
                    <tr><td><strong>Route:</strong></td><td><code>{{ $item->route_name ?? '-' }}</code></td></tr>
                    <tr><td><strong>URL:</strong></td><td><code>{{ $item->url ?? '-' }}</code></td></tr>
                    <tr><td><strong>Sort Order:</strong></td><td>{{ $item->sort_order }}</td></tr>
                    <tr><td><strong>Active:</strong></td><td>{{ $item->is_active ? 'Yes' : 'No' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.menus.index') }}" class="btn btn-secondary mt-3">← Back to Menu Management</a>
@endsection
