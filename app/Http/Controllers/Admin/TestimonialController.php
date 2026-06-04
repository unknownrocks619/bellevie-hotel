<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Services\ImageService;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    private ImageService $images;

    public function __construct(ImageService $images)
    {
        $this->images = $images;
    }

    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order')->paginate(15);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_name'      => 'required|string',
            'guest_title'     => 'nullable|string',
            'guest_country'   => 'nullable|string',
            'content'         => 'required|string',
            'rating'          => 'required|integer|min:1|max:5',
            'guest_avatar_id' => 'nullable|exists:images,id',
            'is_active'       => 'boolean',
            'is_featured'     => 'boolean',
        ]);

        $data = $request->except('guest_avatar_id');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active');

        // Resolve avatar URL from images table (keeps guest_avatar column populated for backward compat)
        if ($request->filled('guest_avatar_id')) {
            $img = $this->images->find((int) $request->guest_avatar_id);
            if ($img) {
                $data['guest_avatar'] = $img->url;
            }
        }

        $testimonial = Testimonial::create($data);

        // Save image relation
        if ($request->filled('guest_avatar_id')) {
            $this->images->attach($testimonial, (int) $request->guest_avatar_id, 'avatar');
        }

        return back()->with('success', 'Testimonial created');
    }

    public function edit(Testimonial $testimonial)
    {
        $avatarImage = $this->images->first($testimonial, 'avatar');
        return view('admin.testimonials.edit', compact('testimonial', 'avatarImage'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'guest_name'      => 'required|string',
            'guest_title'     => 'nullable|string',
            'guest_country'   => 'nullable|string',
            'content'         => 'required|string',
            'rating'          => 'required|integer|min:1|max:5',
            'guest_avatar_id' => 'nullable|exists:images,id',
            'is_active'       => 'boolean',
            'is_featured'     => 'boolean',
        ]);

        $data = $request->except('guest_avatar_id');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active');

        if ($request->filled('guest_avatar_id')) {
            $img = $this->images->find((int) $request->guest_avatar_id);
            if ($img) {
                $data['guest_avatar'] = $img->url;
            }
            $this->images->attach($testimonial, (int) $request->guest_avatar_id, 'avatar');
        }

        $testimonial->update($data);
        return back()->with('success', 'Testimonial updated');
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->images->detach($testimonial);
        $testimonial->delete();
        return back()->with('success', 'Testimonial deleted');
    }
}
