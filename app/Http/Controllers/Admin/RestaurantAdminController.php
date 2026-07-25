<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantPage;
use App\Services\ImageService;
use Illuminate\Http\Request;

class RestaurantAdminController extends Controller
{
    private ImageService $images;

    public function __construct(ImageService $images)
    {
        $this->images = $images;
    }

    public function edit()
    {
        $page          = RestaurantPage::singleton();
        $featuredImage = $this->images->first($page, 'featured');

        return view('admin.restaurant.edit', compact('page', 'featuredImage'));
    }

    public function update(Request $request)
    {
        $page = RestaurantPage::singleton();

        $data = $request->validate([
            'hero_title'        => 'required|string|max:255',
            'hero_subtitle'     => 'nullable|string|max:255',
            'intro_title'       => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'opening_hours'     => 'nullable|string',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
            'image_id'          => 'nullable|exists:images,id',
        ]);

        unset($data['image_id']);
        $data['is_active'] = $request->boolean('is_active');

        $page->update($data);

        if ($request->filled('image_id')) {
            $this->images->attach($page, (int) $request->image_id, 'featured');
        }

        return redirect()->route('admin.restaurant.edit')->with('success', 'Restaurant page updated.');
    }
}
