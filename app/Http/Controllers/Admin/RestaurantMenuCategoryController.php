<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestaurantMenuCategoryController extends Controller
{
    public function index()
    {
        $categories = RestaurantMenuCategory::withCount('items')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.restaurant.categories.index', compact('categories'));
    }

    /** POST /admin/restaurant/categories/reorder — persists drag-and-drop order. */
    public function reorder(Request $request)
    {
        $request->validate([
            'items'      => 'required|array',
            'items.*.id' => 'required|integer|exists:restaurant_menu_categories,id',
        ]);

        foreach ($request->items as $i => $row) {
            RestaurantMenuCategory::where('id', $row['id'])->update(['sort_order' => $i]);
        }

        return response()->json(['success' => true]);
    }

    public function create()
    {
        return view('admin.restaurant.categories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        RestaurantMenuCategory::create($data);

        return redirect()->route('admin.restaurant.categories.index')->with('success', 'Menu category created.');
    }

    public function edit(RestaurantMenuCategory $category)
    {
        $items = $category->items()->get();
        return view('admin.restaurant.categories.edit', compact('category', 'items'));
    }

    public function update(Request $request, RestaurantMenuCategory $category)
    {
        $data = $this->validated($request, $category->id);
        $category->update($data);

        return redirect()->route('admin.restaurant.categories.index')->with('success', 'Menu category updated.');
    }

    public function destroy(RestaurantMenuCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Menu category deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:restaurant_menu_categories,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } else {
            unset($data['slug']);
        }

        return $data;
    }
}
