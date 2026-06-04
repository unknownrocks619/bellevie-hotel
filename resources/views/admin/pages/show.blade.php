@extends('layouts.admin')
@section('page-title', $page->title)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $page->title }}</span>
                <div>
                    <a href="{{ route('admin.pages.edit',$page) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.pages.destroy',$page) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this page?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <p><strong>Status:</strong> <span class="badge bg-{{ $page->status=='published'?'success':'secondary' }}">{{ ucfirst($page->status) }}</span></p>
                <p><strong>Active:</strong> {{ $page->is_active ? 'Yes' : 'No' }}</p>
                <hr>
                <h5>Content</h5>
                <div class="border-top pt-3">
                    {!! nl2br(e($page->content)) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Page Info</div>
            <div class="card-body">
                <p><strong>Slug:</strong><br><code>{{ $page->slug }}</code></p>
                <p><strong>Meta Title:</strong><br>{{ $page->meta_title ?? '-' }}</p>
                <p><strong>Meta Description:</strong><br><small>{{ $page->meta_description ?? '-' }}</small></p>
                <p><strong>Created:</strong><br>{{ $page->created_at->format('M d, Y H:i') }}</p>
                <p><strong>Updated:</strong><br>{{ $page->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.pages.index') }}" class="btn btn-secondary mt-3">← Back to Pages</a>
@endsection
