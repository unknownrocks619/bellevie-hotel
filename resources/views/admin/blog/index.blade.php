@extends('layouts.admin')
@section('page-title', 'Blog Posts')
@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary" style="background:#C9A227;border:none;"><i class="bi bi-plus-circle"></i> New Post</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm datatable">
                <thead><tr>
                    <th>Title</th><th>Category</th><th>Status</th><th>Published At</th><th>Featured</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>
                        <a href="{{ route('admin.blog.show',$post) }}">{{ $post->title }}</a>
                        <br><small class="text-muted">{{ Str::slug($post->title) }}</small>
                    </td>
                    <td>
                        @if($post->category)
                        <span class="badge bg-light text-dark">{{ $post->category->name ?? '-' }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @php $statusColors=['draft'=>'secondary','published'=>'success']; @endphp
                        <span class="badge bg-{{ $statusColors[$post->status]??'secondary' }}">{{ ucfirst($post->status) }}</span>
                    </td>
                    <td>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('M d, Y') : '-' }}</td>
                    <td>
                        @if($post->is_featured)
                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Featured</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.blog.edit',$post) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.blog.destroy',$post) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this post?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No posts found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())
        {{ $posts->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
@endsection
