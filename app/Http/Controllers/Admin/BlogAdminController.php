<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\SysSeo;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlogAdminController extends Controller
{
    private ImageService $images;

    public function __construct(ImageService $images)
    {
        $this->images = $images;
    }

    public function index(Request $request)
    {
        $query = BlogPost::with(['category', 'user']);

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $posts = $query->orderByDesc('created_at')->paginate(15);
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        $seo        = null;
        return view('admin.blog.create', compact('categories', 'seo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'required|string',
            'excerpt'            => 'nullable|string',
            'content'            => 'required|string',
            'featured_image_id'  => 'nullable|exists:images,id',
            'status'             => 'required|in:draft,published,archived',
            'blog_category_id'   => 'nullable|exists:blog_categories,id',
            'meta_title'         => 'nullable|string',
            'meta_description'   => 'nullable|string',
            'is_featured'        => 'boolean',
            'seo_image_id'       => 'nullable|exists:images,id',
            'title_seo'          => 'nullable|string|max:70',
            'description_seo'    => 'nullable|string',
            'tags_seo'           => 'nullable|string',
        ]);

        $data            = $request->except('featured_image_id', 'seo_image_id', 'title_seo', 'description_seo', 'tags_seo');
        $data['slug']    = Str::slug($request->title);
        $data['user_id'] = Auth::id();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        // Resolve featured image URL for backward compat
        if ($request->filled('featured_image_id')) {
            $img = $this->images->find((int) $request->featured_image_id);
            if ($img) {
                $data['featured_image'] = $img->url;
            }
        }

        $post = BlogPost::create($data);

        // Save featured image relation
        if ($request->filled('featured_image_id')) {
            $this->images->attach($post, (int) $request->featured_image_id, 'featured');
        }

        $this->saveSeo($request, $post);

        return redirect()->route('admin.blog.index')->with('success', 'Post created successfully');
    }

    public function show(BlogPost $blog)
    {
        return view('admin.blog.show', ['post' => $blog]);
    }

    public function edit(BlogPost $blog)
    {
        $categories    = BlogCategory::all();
        $post          = $blog;
        $seo           = SysSeo::forModel($blog);
        $featuredImage = $this->images->first($blog, 'featured');
        return view('admin.blog.edit', compact('post', 'categories', 'seo', 'featuredImage'));
    }

    public function update(Request $request, BlogPost $blog)
    {
        $request->validate([
            'title'              => 'required|string',
            'excerpt'            => 'nullable|string',
            'content'            => 'required|string',
            'featured_image_id'  => 'nullable|exists:images,id',
            'status'             => 'required|in:draft,published,archived',
            'blog_category_id'   => 'nullable|exists:blog_categories,id',
            'meta_title'         => 'nullable|string',
            'meta_description'   => 'nullable|string',
            'is_featured'        => 'boolean',
            'seo_image_id'       => 'nullable|exists:images,id',
            'title_seo'          => 'nullable|string|max:70',
            'description_seo'    => 'nullable|string',
            'tags_seo'           => 'nullable|string',
        ]);

        $data = $request->except('featured_image_id', 'seo_image_id', 'title_seo', 'description_seo', 'tags_seo');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->status === 'published' && !$blog->published_at) {
            $data['published_at'] = now();
        }

        if ($request->filled('featured_image_id')) {
            $img = $this->images->find((int) $request->featured_image_id);
            if ($img) {
                $data['featured_image'] = $img->url;
            }
            $this->images->attach($blog, (int) $request->featured_image_id, 'featured');
        }

        $blog->update($data);
        $this->saveSeo($request, $blog);

        return redirect()->route('admin.blog.index')->with('success', 'Post updated successfully');
    }

    public function destroy(BlogPost $blog)
    {
        $seo = SysSeo::forModel($blog);
        if ($seo) { $seo->deleteImage(); $seo->delete(); }
        $this->images->detach($blog);
        $blog->delete();
        return redirect()->route('admin.blog.index')->with('success', 'Post deleted');
    }

    // ─── SEO helper ───────────────────────────────────────────────────────────

    private function saveSeo(Request $request, BlogPost $post): void
    {
        if (!$request->filled('title_seo') && !$request->filled('description_seo')
            && !$request->filled('tags_seo') && !$request->filled('seo_image_id')) {
            return;
        }

        $data = [
            'title_seo'       => $request->title_seo,
            'description_seo' => $request->description_seo,
            'tags_seo'        => $request->tags_seo,
        ];

        if ($request->filled('seo_image_id')) {
            $data['feature_image_seo'] = $request->seo_image_id;
        }

        SysSeo::saveFor($post, $data);
    }
}
