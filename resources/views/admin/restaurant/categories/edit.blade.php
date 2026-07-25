@extends('layouts.admin')
@section('page-title', 'Edit Menu Category')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header p-0">
        <ul class="nav nav-tabs card-header-tabs px-3 pt-2" id="categoryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane"
                        type="button" role="tab" aria-controls="details-pane" aria-selected="true">
                    <i class="bi bi-pencil-square me-1"></i>Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="items-tab" data-bs-toggle="tab" data-bs-target="#items-pane"
                        type="button" role="tab" aria-controls="items-pane" aria-selected="false">
                    <i class="bi bi-egg-fried me-1"></i>Menu Items <span class="badge bg-secondary ms-1">{{ $items->count() }}</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="categoryTabsContent">

        {{-- Details tab --}}
        <div class="tab-pane fade show active p-4" id="details-pane" role="tabpanel" aria-labelledby="details-tab">
            <form action="{{ route('admin.restaurant.categories.update', $category) }}" method="POST">
                @csrf @method('PUT')
                @include('admin.restaurant.categories._form')
                <button class="btn text-white" style="background:#C9A227;border:none;">Save Changes</button>
                <a href="{{ route('admin.restaurant.categories.index') }}" class="btn btn-secondary">Back to List</a>
            </form>
        </div>

        {{-- Menu Items tab --}}
        <div class="tab-pane fade p-4" id="items-pane" role="tabpanel" aria-labelledby="items-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">All menu items in the <strong>{{ $category->name }}</strong> category.</span>
                <a href="{{ route('admin.restaurant.menu-items.create', ['category_id' => $category->id]) }}"
                   class="btn btn-sm text-white" style="background:#C9A227;border:none;">
                    <i class="bi bi-plus-circle me-1"></i>Add Menu Item
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th style="width:56px;"></th>
                            <th>Name</th>
                            <th style="width:90px;">Price</th>
                            <th style="width:90px;">Status</th>
                            <th style="width:110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>
                                @if($item->image_url)
                                <img src="{{ $item->image_url }}" alt="" style="width:44px;height:34px;object-fit:cover;border-radius:4px;">
                                @else
                                <div style="width:44px;height:34px;border-radius:4px;background:#C9A22720;display:flex;align-items:center;justify-content:center;color:#C9A227;">
                                    <i class="bi bi-egg-fried"></i>
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    {{ $item->name }}
                                    @if($item->is_featured)
                                    <i class="bi bi-star-fill" style="color:#C9A227;font-size:0.72rem;" title="Featured"></i>
                                    @endif
                                    @if(!$item->show_price)
                                    <span class="badge bg-secondary" style="font-size:.65rem;">Price hidden</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ Str::limit($item->description, 60) }}</small>
                            </td>
                            <td class="text-muted small">{{ $item->price !== null ? '$'.number_format((float) $item->price, 2) : '—' }}</td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge text-white" style="background:#C9A227;">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.restaurant.menu-items.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.restaurant.menu-items.destroy', $item) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this menu item?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Del</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No menu items in this category yet.
                                <a href="{{ route('admin.restaurant.menu-items.create', ['category_id' => $category->id]) }}">Add the first one</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-muted small mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>To reorder items, use the
                <a href="{{ route('admin.restaurant.menu-items.index', ['category_id' => $category->id]) }}">Menu Items</a> page.
            </p>
        </div>

    </div>
</div>
@endsection
