<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenuCategory;
use App\Models\RestaurantMenuItem;
use App\Models\RestaurantPage;

class RestaurantController extends Controller
{
    public function index()
    {
        $page = RestaurantPage::singleton();
        abort_unless($page->is_active, 404);

        $categories = RestaurantMenuCategory::active()
            ->with(['items' => fn ($q) => $q->active()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($category) => $category->items->isNotEmpty());

        $featuredItems = RestaurantMenuItem::active()->featured()
            ->with('category')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        return view('frontend.restaurant.index', compact('page', 'categories', 'featuredItems'));
    }
}
