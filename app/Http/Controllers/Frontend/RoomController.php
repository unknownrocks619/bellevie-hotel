<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SysSeo;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::active()->with('roomType');

        if ($request->has('type') && $request->type) {
            $query->whereHas('roomType', fn($q) => $q->where('slug', $request->type));
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        if ($request->has('adults') && $request->adults) {
            $query->where('max_adults', '>=', $request->adults);
        }

        $rooms = $query->paginate(9);
        $roomTypes = RoomType::active()->withCount('rooms')->get();

        return view('frontend.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function show(Room $room)
    {
        if (!$room->is_active) {
            abort(404);
        }

        $room->load(['roomType', 'amenities']);

        $similarRooms = Room::active()
            ->where('room_type_id', $room->room_type_id)
            ->where('id', '!=', $room->id)
            ->limit(3)
            ->get();

        // SEO data
        $seo            = SysSeo::forModel($room);
        $seoTitle       = $seo?->title_seo       ?: $room->name;
        $seoDescription = $seo?->description_seo  ?: $room->description;
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
            $seoImage = $room->featuredImageUrl($room->featured_image ?? '');
        }

        return view('frontend.rooms.show', compact('room', 'similarRooms', 'seoTitle', 'seoDescription', 'seoImage'));
    }
}
