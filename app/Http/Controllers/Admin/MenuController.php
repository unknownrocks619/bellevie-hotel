<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\SysSeo;
use App\Models\Image;
use App\Models\Page;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $headerMenu  = Menu::where('location', 'header')->first();
        $footerMenu  = Menu::where('location', 'footer')->first();

        $headerItems = $headerMenu
            ? MenuItem::where('menu_id', $headerMenu->id)->whereNull('parent_id')->orderBy('sort_order')->get()
            : collect();

        $footerItems = $footerMenu
            ? MenuItem::where('menu_id', $footerMenu->id)->whereNull('parent_id')->orderBy('sort_order')->get()
            : collect();

        return view('admin.menus.index', compact('headerItems', 'footerItems'));
    }

    public function create()
    {
        $seo = null;
        $pages = Page::where('is_active', true)->orderBy('title')->get();
        $blogPosts = BlogPost::published()->orderByDesc('published_at')->limit(50)->get();
        $blogCategories = BlogCategory::active()->orderBy('name')->get();
        $rooms = Room::active()->orderBy('name')->get();
        return view('admin.menus.create', compact('seo', 'pages', 'blogPosts', 'blogCategories', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'location'   => 'required|in:header,footer,sidebar',
            'title'      => 'required|string|max:255',
            'url'        => 'nullable|string|max:500',
            'route_name' => 'nullable|string|max:255',
            'link_type'  => 'required|in:route,url,page,blog,blog-category,rooms,single-room',
            'link_ref_id' => 'nullable|integer',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
            'seo_image_id'     => 'nullable|exists:images,id',
            'title_seo'        => 'nullable|string|max:70',
            'description_seo'  => 'nullable|string',
            'tags_seo'         => 'nullable|string',
        ]);

        $url = $request->url;
        $routeName = $request->route_name;

        switch ($request->link_type) {
            case 'page':
                $page = Page::find($request->link_ref_id);
                if ($page) { $url = '/page/' . $page->slug; $routeName = null; }
                break;
            case 'blog':
                $post = BlogPost::find($request->link_ref_id);
                if ($post) { $url = '/blog/' . $post->slug; $routeName = null; }
                break;
            case 'blog-category':
                $cat = BlogCategory::find($request->link_ref_id);
                if ($cat) { $url = '/blog/category/' . $cat->slug; $routeName = null; }
                break;
            case 'rooms':
                $url = '/rooms'; $routeName = 'rooms.index';
                break;
            case 'single-room':
                $room = Room::find($request->link_ref_id);
                if ($room) { $url = '/rooms/' . $room->slug; $routeName = null; }
                break;
        }

        // Find or create the parent Menu record for this location
        $menu = Menu::firstOrCreate(
            ['location' => $request->location],
            ['name' => ucfirst($request->location) . ' Menu', 'is_active' => true]
        );

        $nextOrder = MenuItem::where('menu_id', $menu->id)->max('sort_order') + 1;

        $item = MenuItem::create([
            'menu_id'    => $menu->id,
            'title'      => $request->title,
            'url'        => $url,
            'route_name' => $routeName,
            'link_type' => $request->post('link_type'),
            'link_type_ref_id'      => $request->post('link_ref_id'),

            'sort_order' => $nextOrder,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        $this->saveSeo($request, $item);

        return redirect()->route('admin.menus.index')->with('success', 'Menu item added successfully.');
    }

    // Route model binding: {menu} → MenuItem (type hint resolves the model)
    public function edit(MenuItem $menu)
    {
        $seo = SysSeo::forModel($menu);
        $pages = Page::where('is_active', true)->orderBy('title')->get();
        $blogPosts = BlogPost::published()->orderByDesc('published_at')->limit(50)->get();
        $blogCategories = BlogCategory::active()->orderBy('name')->get();
        $rooms = Room::active()->orderBy('name')->get();
        return view('admin.menus.edit', ['item' => $menu, 'seo' => $seo, 'pages' => $pages, 'blogPosts' => $blogPosts, 'blogCategories' => $blogCategories, 'rooms' => $rooms]);
    }

    public function update(Request $request, MenuItem $menu)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'url'        => 'nullable|string|max:500',
            'route_name' => 'nullable|string|max:255',
            'link_type'  => 'required|in:route,url,page,blog,blog-category,rooms,single-room',
            'link_ref_id' => 'nullable|integer',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
            'seo_image_id'     => 'nullable|exists:images,id',
            'title_seo'        => 'nullable|string|max:70',
            'description_seo'  => 'nullable|string',
            'tags_seo'         => 'nullable|string',
        ]);

        $url = $request->url;
        $routeName = $request->route_name;

        switch ($request->link_type) {
            case 'page':
                $page = Page::find($request->link_ref_id);
                if ($page) { $url = '/page/' . $page->slug; $routeName = null; }
                break;
            case 'blog':
                $post = BlogPost::find($request->link_ref_id);
                if ($post) { $url = '/blog/' . $post->slug; $routeName = null; }
                break;
            case 'blog-category':
                $cat = BlogCategory::find($request->link_ref_id);
                if ($cat) { $url = '/blog/category/' . $cat->slug; $routeName = null; }
                break;
            case 'rooms':
                $url = '/rooms'; $routeName = 'rooms.index';
                break;
            case 'single-room':
                $room = Room::find($request->link_ref_id);
                if ($room) { $url = '/rooms/' . $room->slug; $routeName = null; }
                break;
        }

        $menu->update([
            'title'      => $request->title,
            'url'        => $url,
            'route_name' => $routeName,
            'link_type' => $request->post('link_type'),
            'link_type_ref_id'      => $request->post('link_ref_id'),
            'is_active'  => $request->boolean('is_active'),
        ]);

        $this->saveSeo($request, $menu);

        return redirect()->route('admin.menus.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menu)
    {
        // Clean up SEO record
        $seo = SysSeo::forModel($menu);
        if ($seo) {
            $seo->deleteImage();
            $seo->delete();
        }

        $menu->children()->delete();
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu item deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items'     => 'required|array',
            'items.*.id' => 'required|integer|exists:menu_items,id',
        ]);
        foreach ($request->items as $i => $row) {
            MenuItem::where('id', $row['id'])->update(['sort_order' => $i]);
        }
        return response()->json(['success' => true]);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function saveSeo(Request $request, MenuItem $item): void
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

        SysSeo::saveFor($item, $data);
    }
}
