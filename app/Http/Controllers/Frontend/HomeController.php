<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Testimonial;
use App\Models\BlogPost;
use App\Models\Gallery;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $featuredRooms = Room::active()
            ->featured()
            ->with('roomType')
            ->limit(6)
            ->orderBy('sort_order')
            ->get();

        $testimonials = Testimonial::where('is_active', true)
            ->where('is_featured', true)
            ->limit(6)
            ->orderBy('sort_order')
            ->get();

        $latestPosts = BlogPost::published()
            ->with(['category', 'user'])
            ->limit(3)
            ->orderByDesc('published_at')
            ->get();

        $galleryImages = Gallery::active()
            ->limit(8)
            ->orderBy('sort_order')
            ->with(['imageRelation'])
            ->get();

            $settings = [
            'hotel_name' => Setting::get('hotel_name', 'Bellevie Hotel'),
            'hotel_tagline' => Setting::get('hotel_tagline', 'Where Luxury Meets Serenity'),
            'hotel_description' => Setting::get('hotel_description'),
            'currency_symbol' => Setting::get('currency_symbol', '$'),
        ];

        return view('frontend.home', compact('featuredRooms', 'testimonials', 'latestPosts', 'galleryImages', 'settings'));
    }

    public function about()
    {
        $settings = [
            'hotel_name' => Setting::get('hotel_name', 'Bellevie Hotel'),
            'hotel_description' => Setting::get('hotel_description'),
            'hotel_address' => Setting::get('hotel_address'),
            'hotel_city' => Setting::get('hotel_city'),
            'hotel_country' => Setting::get('hotel_country'),
        ];

        $stats = [
            'years_since' => 2005,
            'room_count' => Room::active()->count(),
            'guest_count' => 15000,
        ];

        return view('frontend.about', compact('settings', 'stats'));
    }
}
