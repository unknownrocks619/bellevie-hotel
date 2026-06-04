@extends('layouts.admin')
@section('page-title', 'Add Menu Item')
@section('content')

    <div class="row mb-3">
        <div class="col">
            <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to Menus
            </a>
        </div>
    </div>

    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-0" id="menuTabs" style="border-bottom:none;">
            <li class="nav-item">
                <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tabDetails">
                    <i class="bi bi-list-ul me-1"></i>Details
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tabSeo">
                    <i class="bi bi-search me-1"></i>SEO
                </button>
            </li>
        </ul>

        <div class="card" style="border-top-left-radius:0;">
            <div class="card-body tab-content pt-4">

                {{-- Details Tab --}}
                <div class="tab-pane fade show active" id="tabDetails">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Menu Location <span class="text-danger">*</span></label>
                        <select name="location" class="form-select @error('location') is-invalid @enderror" required>
                            <option value="">— Select Location —</option>
                            <option value="header" {{ old('location', request('menu')) == 'header' ? 'selected' : '' }}>
                                Header Menu</option>
                            <option value="footer" {{ old('location', request('menu')) == 'footer' ? 'selected' : '' }}>
                                Footer Menu</option>
                            <option value="sidebar" {{ old('location') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                        </select>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}" required placeholder="e.g. Rooms, About Us">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Link Type</label>
                        <select name="link_type" id="link_type" class="form-select"
                            onchange="menuLinkTypeChanged(this.value)">
                            <option value="route" {{ old('link_type', 'route') == 'route' ? 'selected' : '' }}>Route Name
                            </option>
                            <option value="url" {{ old('link_type') == 'url' ? 'selected' : '' }}>Custom URL</option>
                            <option value="page" {{ old('link_type') == 'page' ? 'selected' : '' }}>Page</option>
                            <option value="blog" {{ old('link_type') == 'blog' ? 'selected' : '' }}>Blog Post</option>
                            <option value="blog-category" {{ old('link_type') == 'blog-category' ? 'selected' : '' }}>Blog
                                Category</option>
                            <option value="rooms" {{ old('link_type') == 'rooms' ? 'selected' : '' }}>All Rooms</option>
                            <option value="single-room" {{ old('link_type') == 'single-room' ? 'selected' : '' }}>Single
                                Room</option>
                        </select>
                    </div>

                    {{-- Route name field --}}
                    <div class="mb-3 link-field" id="field_route">
                        <label class="form-label fw-semibold">Route Name</label>
                        <input type="text" name="route_name"
                            class="form-control @error('route_name') is-invalid @enderror" value="{{ old('route_name') }}"
                            placeholder="e.g. home, rooms.index, about">
                        @error('route_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Use a Laravel named route. Leave blank if using Custom URL.</small>
                    </div>

                    {{-- Custom URL field --}}
                    <div class="mb-3 link-field" id="field_url" style="display:none;">
                        <label class="form-label fw-semibold">Custom URL</label>
                        <input type="text" name="url" class="form-control @error('url') is-invalid @enderror"
                            value="{{ old('url') }}" placeholder="e.g. https://example.com or /contact">
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
                                <option value="{{ $p->id }}" {{ old('link_ref_id') == $p->id ? 'selected' : '' }}>
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
                                value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>New items are added at the end. Reorder them by dragging
                            on the
                            <a href="{{ route('admin.menus.index') }}">Menu Management</a> page.
                        </small>
                    </div>
                </div>

                {{-- SEO Tab --}}
                <div class="tab-pane fade" id="tabSeo">
                    @include('admin.partials.seo-tab', ['seo' => $seo])
                </div>

            </div>

            <div class="card-footer border-top d-flex gap-2">
                <button type="submit" class="btn" style="background:#C9A227;color:white;border:none;">
                    <i class="bi bi-plus-circle me-1"></i>Create Menu Item
                </button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>

    <script>
        function menuLinkTypeChanged(type) {
            document.querySelectorAll('.link-field').forEach(el => el.style.display = 'none');
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
@endsection
