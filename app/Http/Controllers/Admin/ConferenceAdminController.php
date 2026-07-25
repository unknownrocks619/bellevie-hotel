<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConferencePage;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ConferenceAdminController extends Controller
{
    private ImageService $images;

    public function __construct(ImageService $images)
    {
        $this->images = $images;
    }

    public function edit()
    {
        $page          = ConferencePage::singleton();
        $featuredImage = $this->images->first($page, 'featured');
        $galleryImages = $this->images->all($page, 'gallery');

        return view('admin.conference.edit', compact('page', 'featuredImage', 'galleryImages'));
    }

    public function update(Request $request)
    {
        $page = ConferencePage::singleton();

        $data = $request->validate([
            'hero_title'        => 'required|string|max:255',
            'hero_subtitle'     => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'capacity_text'     => 'nullable|string|max:255',
            'layout_text'       => 'nullable|string|max:255',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
            'image_id'          => 'nullable|exists:images,id',
            'gallery_image_ids'   => 'nullable|array',
            'gallery_image_ids.*' => 'exists:images,id',
        ]);

        unset($data['image_id'], $data['gallery_image_ids']);
        $data['is_active'] = $request->boolean('is_active');

        $page->update($data);

        if ($request->filled('image_id')) {
            $this->images->attach($page, (int) $request->image_id, 'featured');
        }
        if ($request->filled('gallery_image_ids')) {
            $this->images->attachMany($page, $request->gallery_image_ids, 'gallery');
        }

        return redirect()->route('admin.conference.edit')->with('success', 'Conference page updated.');
    }
}
