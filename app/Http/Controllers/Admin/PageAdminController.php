<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\SysSeo;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PageAdminController extends Controller
{
    private bool $cloudinaryEnabled;

    public function __construct()
    {
        $this->cloudinaryEnabled = !empty(config('cloudinary.cloud_name')) &&
                                   !empty(config('cloudinary.api_key')) &&
                                   !empty(config('cloudinary.api_secret'));
    }

    private function uploadSeoImage($file): string
    {
        if ($this->cloudinaryEnabled) {
            try {
                $cloudinary = new \App\Services\CloudinaryService();
                $result = $cloudinary->upload($file, 'seo');
                return $result['url'];
            } catch (\Exception $e) { /* fall through */ }
        }
        $path = $file->store('seo', 'public');
        return asset('storage/' . $path);
    }

    public function index()
    {
        $pages = Page::orderBy('title')->paginate(20);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $seo = null;
        return view('admin.pages.create', compact('seo'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'title'            => 'required|string',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string',
            'meta_description' => 'nullable|string',
            'sort_order'       => 'nullable|integer|min:0',
            'seo_image_id'     => 'nullable|exists:images,id',
            'title_seo'        => 'nullable|string|max:70',
            'description_seo'  => 'nullable|string',
            'tags_seo'         => 'nullable|string',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->title);

        $page = Page::create([
            'title'            => $request->title,
            'slug'             => $slug,
            'content'          => $request->content,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active'        => $request->boolean('is_active'),
            'show_in_nav'      => $request->boolean('show_in_nav'),
            'sort_order'       => $request->input('sort_order', 0),
        ]);

        $this->saveSeo($request, $page);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        $seo = SysSeo::forModel($page);
        return view('admin.pages.edit', compact('page', 'seo'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title'            => 'required|string',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string',
            'meta_description' => 'nullable|string',
            'sort_order'       => 'nullable|integer|min:0',
            'seo_image_id'     => 'nullable|exists:images,id',
            'title_seo'        => 'nullable|string|max:70',
            'description_seo'  => 'nullable|string',
            'tags_seo'         => 'nullable|string',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->title);

        $page->update([
            'title'            => $request->title,
            'slug'             => $slug,
            'content'          => $request->content,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active'        => $request->boolean('is_active'),
            'show_in_nav'      => $request->boolean('show_in_nav'),
            'sort_order'       => $request->input('sort_order', $page->sort_order),
        ]);

        $this->saveSeo($request, $page);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $seo = SysSeo::forModel($page);
        if ($seo) { $seo->deleteImage(); $seo->delete(); }
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted');
    }

    private function saveSeo(Request $request, Page $page): void
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

        SysSeo::saveFor($page, $data);
    }
}
