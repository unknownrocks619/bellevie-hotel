@extends('layouts.admin')
@section('page-title', 'FAQ Management')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Toolbar --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        {{-- Category tabs --}}
        <a href="{{ route('admin.faqs.index') }}"
           class="btn btn-sm {{ !request('category') ? 'text-white' : 'btn-outline-secondary' }}"
           style="{{ !request('category') ? 'background:#C9A227;border:none;' : '' }}">
            All
        </a>
        @foreach($categories as $slug => $label)
        <a href="{{ route('admin.faqs.index', ['category' => $slug]) }}"
           class="btn btn-sm {{ request('category') === $slug ? 'text-white' : 'btn-outline-secondary' }}"
           style="{{ request('category') === $slug ? 'background:#C9A227;border:none;' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="d-flex gap-2">
        <form class="d-flex gap-2" method="GET" action="{{ route('admin.faqs.index') }}">
            @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search…" value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-sm text-white" style="background:#C9A227;border:none;">
            <i class="bi bi-plus-circle me-1"></i>New FAQ
        </a>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Question</th>
                    <th style="width:130px;">Category</th>
                    <th style="width:80px;">Order</th>
                    <th style="width:90px;">Status</th>
                    <th style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                <tr>
                    <td class="text-muted small">{{ $faq->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ Str::limit($faq->title, 80) }}</div>
                        <small class="text-muted">{{ Str::limit(strip_tags($faq->description), 80) }}</small>
                    </td>
                    <td>
                        <span class="badge rounded-pill" style="background:#C9A22720;color:#C9A227;border:1px solid #C9A22740;font-size:0.72rem;">
                            {{ $faq->category_label }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $faq->sort_order }}</td>
                    <td>
                        @if($faq->is_active)
                            <span class="badge text-white" style="background:#C9A227;">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this FAQ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Del</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-question-circle" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        No FAQs yet.
                        <a href="{{ route('admin.faqs.create') }}">Add the first one</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($faqs->hasPages())
<div class="mt-3">
    {{ $faqs->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection
