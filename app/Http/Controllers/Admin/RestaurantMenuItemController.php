<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenuCategory;
use App\Models\RestaurantMenuItem;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestaurantMenuItemController extends Controller
{
    private ImageService $images;

    public function __construct(ImageService $images)
    {
        $this->images = $images;
    }

    public function index(Request $request)
    {
        $categoriesQuery = RestaurantMenuCategory::with(['items' => function ($q) use ($request) {
                if ($request->filled('search')) {
                    $q->where('name', 'like', "%{$request->search}%");
                }
            }])
            ->orderBy('sort_order')->orderBy('name');

        if ($request->filled('category_id')) {
            $categoriesQuery->where('id', $request->category_id);
        }

        $groups         = $categoriesQuery->get();
        $categories     = RestaurantMenuCategory::orderBy('name')->pluck('name', 'id');

        return view('admin.restaurant.menu-items.index', compact('groups', 'categories'));
    }

    /** POST /admin/restaurant/menu-items/reorder — persists drag-and-drop order within a category. */
    public function reorder(Request $request)
    {
        $request->validate([
            'items'      => 'required|array',
            'items.*.id' => 'required|integer|exists:restaurant_menu_items,id',
        ]);

        foreach ($request->items as $i => $row) {
            RestaurantMenuItem::where('id', $row['id'])->update(['sort_order' => $i]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleStatus(RestaurantMenuItem $menuItem)
    {
        $menuItem->update(['is_active' => !$menuItem->is_active]);
        return back()->with('success', 'Menu item status updated.');
    }

    public function create(Request $request)
    {
        $categories         = RestaurantMenuCategory::orderBy('name')->pluck('name', 'id');
        $selectedCategoryId = $request->integer('category_id') ?: null;
        return view('admin.restaurant.menu-items.create', compact('categories', 'selectedCategoryId'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->filled('image_id')) {
            $img = $this->images->find((int) $request->image_id);
            if ($img) $data['image_url'] = $img->url;
        }

        $item = RestaurantMenuItem::create($data);

        if ($request->filled('image_id')) {
            $this->images->attach($item, (int) $request->image_id, 'featured');
        }

        return redirect()->route('admin.restaurant.menu-items.index')->with('success', 'Menu item created.');
    }

    public function edit(RestaurantMenuItem $menuItem)
    {
        $categories    = RestaurantMenuCategory::orderBy('name')->pluck('name', 'id');
        $featuredImage = $this->images->first($menuItem, 'featured');

        return view('admin.restaurant.menu-items.edit', compact('menuItem', 'categories', 'featuredImage'));
    }

    public function update(Request $request, RestaurantMenuItem $menuItem)
    {
        $data = $this->validated($request, $menuItem->id);

        if ($request->filled('image_id')) {
            $img = $this->images->find((int) $request->image_id);
            if ($img) $data['image_url'] = $img->url;
            $this->images->attach($menuItem, (int) $request->image_id, 'featured');
        }

        $menuItem->update($data);

        return redirect()->route('admin.restaurant.menu-items.index')->with('success', 'Menu item updated.');
    }

    public function destroy(RestaurantMenuItem $menuItem)
    {
        $this->images->detach($menuItem);
        $menuItem->delete();
        return back()->with('success', 'Menu item deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'category_id'   => 'required|exists:restaurant_menu_categories,id',
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:restaurant_menu_items,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
            'description'   => 'nullable|string',
            'price'         => 'nullable|numeric|min:0',
            'dietary_tags'  => 'nullable|string|max:255',
            'sort_order'    => 'nullable|integer|min:0',
            'image_id'      => 'nullable|exists:images,id',
        ]);

        unset($data['image_id']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active');
        $data['show_price']  = $request->boolean('show_price');
        $data['sort_order']  = (int) ($data['sort_order'] ?? 0);
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } else {
            unset($data['slug']);
        }

        return $data;
    }
}
