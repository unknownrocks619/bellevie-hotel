@extends('layouts.admin')
@section('page-title', 'Edit Blog Post')
@section('content')

    <form action="{{ route('admin.blog.update', $post) }}" method="POST">
        @csrf @method('PUT')

        {{-- Tab nav --}}
        <ul class="nav nav-tabs mb-0" style="border-bottom:none;">
            <li class="nav-item">
                <button class="nav-link active px-4 text-dark" type="button" data-bs-toggle="tab" data-bs-target="#tabDetails">
                    <i class="bi bi-file-text me-2"></i>Post Details
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
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $post->title) }}" required onkeyup="generateSlug()">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug"
                            class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $post->slug) }}"
                            required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Auto-generated from title</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" rows="2"
                            placeholder="Brief summary of the post...">{{ old('excerpt', $post->excerpt) }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content (Rich Text)</label>
                        <div id="quill-editor" style="height:350px;"></div>
                        <textarea name="content" id="content" class="d-none">{{ old('content', $post->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="blog_category_id"
                                    class="form-select @error('blog_category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @if (isset($categories))
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('blog_category_id', $post->blog_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('blog_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>
                                        Draft
                                    </option>
                                    <option value="published"
                                        {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <x-image-picker name="featured_image_id" label="Featured Image" type="featured" folder="bellevie_hotel/blog" :value="$featuredImage ?? null" />
                        @error('featured_image_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="featured" value="1"
                                {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Featured Post</label>
                        </div>
                    </div>
                </div>

                {{-- SEO Tab --}}
                <div class="tab-pane fade  bg-white" id="tabSeo">
                    @include('admin.partials.seo-tab', ['seo' => $seo])
                </div>

            </div>

            <div class="card-footer border-top d-flex gap-2">
                <button class="btn" style="background:#C9A227;color:white;border:none;">
                    <i class="bi bi-save me-1"></i>Update Post
                </button>
                <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>

    <!-- Quill Editor -->
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

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
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
