@extends('layouts.admin')
@section('page-title', 'Edit Menu Item')
@section('content')

    <div class="row mb-3">
        <div class="col">
            <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to Menus
            </a>
        </div>
    </div>

    <form action="{{ route('admin.menus.update', $item) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-0" id="menuTabs" style="border-bottom:none;">
            <li class="nav-item">
                <button class="nav-link active px-4 text-dark" type="button" data-bs-toggle="tab"
                    data-bs-target="#tabDetails">
                    <i class="bi bi-list-ul me-2"></i>Details
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $seo ? 'text-success' : '' }} px-4 text-dark" type="button" data-bs-toggle="tab"
                    data-bs-target="#tabSeo">
                    <i class="bi bi-search me-2"></i>SEO
                    @if ($seo)
                        <i class="bi bi-check-circle-fill ms-1" style="font-size:0.75rem;"></i>
                    @endif
                </button>
            </li>
        </ul>

        <div class="card" style="border-top-left-radius:0;">
            <div class="card-body tab-content pt-4">

                {{-- Details Tab --}}
                <div class="tab-pane fade show active" id="tabDetails">
                    @php
                        $currentType = 'route';
                        if ($item->link_type) {
                            $currentType = $item->link_type;
                        } elseif ($item->route_name === 'rooms.index') {
                            $currentType = 'rooms';
                        } elseif (old('link_type')) {
                            $currentType = old('link_type');
                        } elseif ($item->url && str_starts_with($item->url, '/page/')) {
                            $currentType = 'page';
                        } elseif ($item->url && str_starts_with($item->url, '/blog/category/')) {
                            $currentType = 'blog-category';
                        } elseif ($item->url && str_starts_with($item->url, '/blog/')) {
                            $currentType = 'blog';
                        } elseif ($item->url && preg_match('#^/rooms/\S#', $item->url)) {
                            $currentType = 'single-room';
                        } elseif ($item->url) {
                            $currentType = 'url';
                        } elseif ($item->route_name) {
                            $currentType = 'route';
                        }
                    @endphp

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $item->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Link Type</label>
                        <select name="link_type" id="link_type" class="form-select"
                            onchange="menuLinkTypeChanged(this.value)">
                            <option value="route" {{ $currentType == 'route' ? 'selected' : '' }}>Route Name</option>
                            <option value="url" {{ $currentType == 'url' ? 'selected' : '' }}>Custom URL</option>
                            <option value="page" {{ $currentType == 'page' ? 'selected' : '' }}>Page</option>
                            <option value="blog" {{ $currentType == 'blog' ? 'selected' : '' }}>Blog Post</option>
                            <option value="blog-category" {{ $currentType == 'blog-category' ? 'selected' : '' }}>Blog
                                Category</option>
                            <option value="rooms" {{ $currentType == 'rooms' ? 'selected' : '' }}>All Rooms</option>
                            <option value="single-room" {{ $currentType == 'single-room' ? 'selected' : '' }}>Single Room
                            </option>
                        </select>
                    </div>

                    {{-- Route name field --}}
                    <div class="mb-3 link-field" id="field_route">
                        <label class="form-label fw-semibold">Route Name</label>
                        <input type="text" name="route_name"
                            class="form-control @error('route_name') is-invalid @enderror"
                            value="{{ old('route_name', $item->route_name) }}" placeholder="e.g. home, rooms.index">
                        @error('route_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Custom URL field --}}
                    <div class="mb-3 link-field" id="field_url" style="display:none;">
                        <label class="form-label fw-semibold">Custom URL</label>
                        <input type="text" name="url" class="form-control @error('url') is-invalid @enderror"
                            value="{{ old('url', $item->attributes['url'] ?? '') }}"
                            placeholder="e.g. https://example.com">
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Page select --}}
                    <div class="mb-3 link-field" id="field_page" style="display:none;">
                        <label class="form-label fw-semibold">Select Page</label>
                        <select name="link_ref_id" class="form-select" id="select_page">
                            <option value="">— Choose a page —</option>
                            @foreach ($pages as $p)
                                <option value="{{ $p->id }}"
                                    {{ (old('link_ref_id') ?? $item->link_type_ref_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Blog post select --}}
                    <div class="mb-3 link-field" id="field_blog" style="display:none;">
                        <label class="form-label fw-semibold">Select Blog Post</label>
                        <select name="link_ref_id" class="form-select" id="select_blog">
                            <option value="">— Choose a post —</option>
                            @foreach ($blogPosts as $p)
                                <option value="{{ $p->id }}" {{ old('link_ref_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Blog category select --}}
                    <div class="mb-3 link-field" id="field_blog-category" style="display:none;">
                        <label class="form-label fw-semibold">Select Blog Category</label>
                        <select name="link_ref_id" class="form-select" id="select_blog-category">
                            <option value="">— Choose a category —</option>
                            @foreach ($blogCategories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('link_ref_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Single room select --}}
                    <div class="mb-3 link-field" id="field_single-room" style="display:none;">
                        <label class="form-label fw-semibold">Select Room</label>
                        <select name="link_ref_id" class="form-select" id="select_single-room">
                            <option value="">— Choose a room —</option>
                            @foreach ($rooms as $r)
                                <option value="{{ $r->id }}" {{ old('link_ref_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- All Rooms info --}}
                    <div class="mb-3 link-field" id="field_rooms" style="display:none;">
                        <div class="alert alert-info py-2 mb-0">
                            <i class="bi bi-info-circle me-1"></i>Links to <code>/rooms</code> — the full rooms listing
                            page.
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="form-check mb-1">
                            <input type="checkbox" name="is_active" class="form-check-input" id="active"
                                value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>To reorder menu items, drag them on the
                            <a href="{{ route('admin.menus.index') }}">Menu Management</a> page.
                        </small>
                    </div>
                </div>

                {{-- SEO Tab --}}
                <div class="tab-pane fade  bg-white" id="tabSeo">
                    @include('admin.partials.seo-tab', ['seo' => $seo])
                </div>

            </div>

            <div class="card-footer border-top d-flex gap-2">
                <button type="submit" class="btn" style="background:#C9A227;color:white;border:none;">
                    <i class="bi bi-save me-1"></i>Update Menu Item
                </button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>

@endsection
@push('page_script')
    <script>
        function menuLinkTypeChanged(type) {
            document.querySelectorAll('.link-field').forEach(el => el.style.display = 'none');
            $('.link-field').find('input').val('');
            const field = document.getElementById('field_' + type);
            if (field) field.style.display = 'block';
        }
        document.addEventListener('DOMContentLoaded', () => {
            const val = document.getElementById('link_type').value;
            menuLinkTypeChanged(val);
        });

        $('form').on('submit', function(e) {
            let _linkField = $('#link_type').find(':selected').val();
            let _selectedFields = $(`#select_${_linkField}`);
            let _shouldBeValue = $(_selectedFields).is('select') == true ?
                $(_selectedFields).find(':selected').val() : $(_selectedFields).val();

            $("[name='link_ref_id']").val(_shouldBeValue);
        })
    </script>
@endpush
