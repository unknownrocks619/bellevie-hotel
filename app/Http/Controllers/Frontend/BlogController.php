<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\Image;
use App\Models\SysSeo;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()
            ->with(['category', 'user'])
            ->orderByDesc('published_at')
            ->paginate(9);

        $categories = BlogCategory::active()
            ->withCount(['posts' => fn($q) => $q->published()])
            ->get();

        $featuredPosts = BlogPost::published()
            ->featured()
            ->limit(3)
            ->orderByDesc('published_at')
            ->get();

        return view('frontend.blog.index', compact('posts', 'categories', 'featuredPosts'));
    }

    public function show(BlogPost $post)
    {
        if ($post->status !== 'published' || !$post->published_at) {
            abort(404);
        }

        $post->increment('views');
        $post->load(['category', 'user']);

        $relatedPosts = BlogPost::published()
            ->where('blog_category_id', $post->blog_category_id)
            ->where('id', '!=', $post->id)
            ->limit(3)
            ->get();

        // SEO data
        $seo            = SysSeo::forModel($post);
        $seoTitle       = $seo?->title_seo       ?: $post->meta_title   ?: $post->title;
        $seoDescription = $seo?->description_seo  ?: $post->meta_description ?: $post->excerpt;
        $seoImage       = '';
        if ($seo?->feature_image_seo) {
            if (is_numeric($seo->feature_image_seo)) {
                $img = Image::find($seo->feature_image_seo);
                $seoImage = $img?->url ?? '';
            } else {
                $seoImage = $seo->feature_image_seo;
            }
        }
        if (!$seoImage) {
            $seoImage = $post->featuredImageUrl($post->featured_image ?? '');
        }

        return view('frontend.blog.show', compact('post', 'relatedPosts', 'seoTitle', 'seoDescription', 'seoImage'));
    }

    public function category(BlogCategory $category)
    {
        $posts = $category->posts()
            ->where('status', 'published')
            ->with(['user', 'category'])
            ->orderByDesc('published_at')
            ->paginate(9);

        $categories = BlogCategory::active()
            ->withCount(['posts' => fn($q) => $q->published()])
            ->get();

        return view('frontend.blog.index', compact('posts', 'categories', 'category'));
    }
}
