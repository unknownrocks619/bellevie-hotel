<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\SysSeo;
use App\Models\Image;
use App\Models\ImageRelation;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomAdminController extends Controller
{
    private ImageService $images;

    public function __construct(ImageService $images)
    {
        $this->images = $images;
    }

    public function index(Request $request)
    {
        $query = Room::with('roomType');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('room_number', 'like', "%{$request->search}%");
        }

        if ($request->type) {
            $query->where('room_type_id', $request->type);
        }

        if ($request->status !== null) {
            $query->where('is_active', (bool) $request->status);
        }

        $rooms     = $query->paginate(15);
        $roomTypes = RoomType::all();

        return view('admin.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function create()
    {
        $roomTypes = RoomType::all();
        $amenities = Amenity::all();
        $seo       = null;
        return view('admin.rooms.create', compact('roomTypes', 'amenities', 'seo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_type_id'         => 'required|exists:room_types,id',
            'name'                 => 'required|string',
            'room_number'          => 'required|unique:rooms',
            'description'          => 'required|string',
            'content'              => 'nullable|string',
            'price_per_night'      => 'required|numeric|min:0',
            'weekend_price'        => 'nullable|numeric|min:0',
            'size_sqft'            => 'nullable|integer',
            'max_adults'           => 'required|integer|min:1',
            'max_children'         => 'required|integer|min:0',
            'bed_type'             => 'required|string',
            'floor'                => 'nullable|integer',
            'view_type'            => 'nullable|string',
            'featured_image_id'    => 'nullable|exists:images,id',
            'gallery_image_ids'    => 'nullable|array',
            'gallery_image_ids.*'  => 'exists:images,id',
            'is_active'            => 'boolean',
            'is_featured'          => 'boolean',
            'amenities'            => 'nullable|array',
            'seo_image_id'         => 'nullable|exists:images,id',
            'title_seo'            => 'nullable|string|max:70',
            'description_seo'      => 'nullable|string',
            'tags_seo'             => 'nullable|string',
        ]);

        $data = $request->except(
            'featured_image_id', 'gallery_image_ids', 'amenities',
            'seo_image_id', 'title_seo', 'description_seo', 'tags_seo'
        );
        $data['slug'] = Str::slug($request->name);

        // Resolve featured image URL from images table (keeps rooms.featured_image populated)
        if ($request->filled('featured_image_id')) {
            $img = Image::find($request->featured_image_id);
            if ($img) $data['featured_image'] = $img->url;
        }

        // Resolve gallery URLs
        if ($request->filled('gallery_image_ids')) {
            $galleryUrls = Image::whereIn('id', $request->gallery_image_ids)->pluck('url')->toArray();
            $data['gallery_images'] = $galleryUrls;
        }

        $room = Room::create($data);

        // Sync amenities
        if ($request->has('amenities')) {
            $room->amenities()->sync($request->amenities);
        }

        // Save image relations
        $this->saveImageRelations($request, $room);

        // Save SEO
        $this->saveSeo($request, $room);

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully');
    }

    public function show(Room $room)
    {
        $room->load(['roomType', 'amenities', 'bookings']);
        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $room->load('amenities');
        $roomTypes = RoomType::all();
        $amenities = Amenity::all();
        $seo       = SysSeo::forModel($room);

        // Load related images for the picker
        $featuredImage = ImageRelation::firstForModel($room, 'featured');
        $galleryImages = $this->images->all($room, 'gallery');

        return view('admin.rooms.edit', compact('room', 'roomTypes', 'amenities', 'seo', 'featuredImage', 'galleryImages'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_type_id'         => 'required|exists:room_types,id',
            'name'                 => 'required|string',
            'room_number'          => 'required|unique:rooms,room_number,' . $room->id,
            'description'          => 'required|string',
            'content'              => 'nullable|string',
            'price_per_night'      => 'required|numeric|min:0',
            'weekend_price'        => 'nullable|numeric|min:0',
            'size_sqft'            => 'nullable|integer',
            'max_adults'           => 'required|integer|min:1',
            'max_children'         => 'required|integer|min:0',
            'bed_type'             => 'required|string',
            'floor'                => 'nullable|integer',
            'view_type'            => 'nullable|string',
            'featured_image_id'    => 'nullable|exists:images,id',
            'gallery_image_ids'    => 'nullable|array',
            'gallery_image_ids.*'  => 'exists:images,id',
            'is_active'            => 'boolean',
            'is_featured'          => 'boolean',
            'amenities'            => 'nullable|array',
            'seo_image_id'         => 'nullable|exists:images,id',
            'title_seo'            => 'nullable|string|max:70',
            'description_seo'      => 'nullable|string',
            'tags_seo'             => 'nullable|string',
        ]);

        $data = $request->except(
            'featured_image_id', 'gallery_image_ids', 'amenities',
            'seo_image_id', 'title_seo', 'description_seo', 'tags_seo'
        );

        if ($request->filled('featured_image_id')) {
            $img = Image::find($request->featured_image_id);
            if ($img) $data['featured_image'] = $img->url;
        }

        if ($request->filled('gallery_image_ids')) {
            $galleryUrls = Image::whereIn('id', $request->gallery_image_ids)->pluck('url')->toArray();
            $data['gallery_images'] = $galleryUrls;
        }

        $room->update($data);

        if ($request->has('amenities')) {
            $room->amenities()->sync($request->amenities);
        }

        $this->saveImageRelations($request, $room);
        $this->saveSeo($request, $room);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully');
    }

    public function destroy(Room $room)
    {
        if ($room->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return back()->withErrors('Cannot delete room with active bookings');
        }

        $seo = SysSeo::forModel($room);
        if ($seo) { $seo->deleteImage(); $seo->delete(); }

        $this->images->detach($room);
        $room->amenities()->detach();
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully');
    }

    public function toggleStatus(Room $room)
    {
        $room->update(['is_active' => !$room->is_active]);
        return back()->with('success', 'Room status updated');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function saveImageRelations(Request $request, Room $room): void
    {
        if ($request->filled('featured_image_id')) {
            $this->images->attach($room, (int) $request->featured_image_id, 'featured');
        }

        if ($request->filled('gallery_image_ids')) {
            $this->images->attachMany($room, $request->gallery_image_ids, 'gallery');
        }
    }

    private function saveSeo(Request $request, Room $room): void
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

        // Store image ID (or URL from image model) in feature_image_seo
        if ($request->filled('seo_image_id')) {
            $img = Image::find($request->seo_image_id);
            if ($img) {
                $data['feature_image_seo'] = $img->id; // store ID for picker
            }
        }

        SysSeo::saveFor($room, $data);
    }
}
