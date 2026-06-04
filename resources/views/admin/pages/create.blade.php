@extends('layouts.admin')
@section('page-title', 'Create Page')
@section('content')

    <form action="{{ route('admin.pages.store') }}" id="page-create-form" method="POST" enctype="multipart/form-data">
        @csrf

        <ul class="nav nav-tabs mb-0" style="border-bottom:none;">
            <li class="nav-item">
                <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tabDetails">
                    <i class="bi bi-file-earmark-text me-1"></i>Page Details
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

                <div class="tab-pane fade show active" id="tabDetails">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="title"
                            class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required
                            onkeyup="generateSlug()">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug"
                            class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Auto-generated from title</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content (Rich Text)</label>
                        <div id="quill-editor" style="height:350px;"></div>
                        <textarea name="content" id="content" class="d-none">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title"
                                    class="form-control @error('meta_title') is-invalid @enderror"
                                    value="{{ old('meta_title') }}" placeholder="SEO title">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control"
                                    value="{{ old('sort_order', 0) }}" min="0">
                                <small class="text-muted">Lower number = appears first</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="2"
                            placeholder="SEO description">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-auto">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="active"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">Active (visible on website)</label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input type="checkbox" name="show_in_nav" class="form-check-input" id="show_in_nav"
                                    value="1" {{ old('show_in_nav') ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_in_nav">Show in navigation</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tabSeo">
                    @include('admin.partials.seo-tab', ['seo' => $seo])
                </div>

            </div>

            <div class="card-footer border-top d-flex gap-2">
                <button class="btn" style="background:#C9A227;color:white;border:none;">
                    <i class="bi bi-plus-circle me-1"></i>Create Page
                </button>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        const quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        header: [1, 2, 3, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        list: 'ordered'
                    }, {
                        list: 'bullet'
                    }],
                    ['link', 'blockquote', 'code-block'],
                    ['clean']
                ]
            },
            placeholder: 'Write your content here…'
        });

        const form = document.getElementById('page-create-form');

        if (form) {

            form.addEventListener('submit', function(e) {
                console.log('quiell element:: ', quill.root.innerHTML);
                document.getElementById('content').value = quill.root.innerHTML;
            });
        }

        const existing = document.getElementById('content').value;
        if (existing) quill.root.innerHTML = existing;

        function generateSlug() {
            const title = document.getElementById('title').value;
            document.getElementById('slug').value = title.toLowerCase()
                .replace(/[^\w\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();
        }
    </script>
@endsection
