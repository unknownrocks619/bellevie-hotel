<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    private ImageService $images;

    public function __construct(ImageService $images)
    {
        $this->images = $images;
    }

    public function index()
    {
        $settings  = Setting::all()->pluck('value', 'key')->toArray();
        $logoImage = null;
        if (!empty($settings['logo_url'])) {
            // Try to find the Image record by URL for the picker to display
            $logoImage = Image::where('url', $settings['logo_url'])->first();
        }
        return view('admin.settings.index', compact('settings', 'logoImage'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hotel_name'           => 'nullable|string',
            'hotel_tagline'        => 'nullable|string',
            'hotel_description'    => 'nullable|string',
            'hotel_email'          => 'nullable|email',
            'hotel_phone'          => 'nullable|string',
            'hotel_address'        => 'nullable|string',
            'hotel_city'           => 'nullable|string',
            'hotel_country'        => 'nullable|string',
            'tax_rate'             => 'nullable|numeric|min:0',
            'currency'             => 'nullable|string',
            'currency_symbol'      => 'nullable|string',
            'check_in_time'        => 'nullable|string',
            'check_out_time'       => 'nullable|string',
            'facebook_url'         => 'nullable|url',
            'instagram_url'        => 'nullable|url',
            'twitter_url'          => 'nullable|url',
            'mail_host'            => 'nullable|string',
            'mail_port'            => 'nullable|integer',
            'mail_username'        => 'nullable|string',
            'mail_password'        => 'nullable|string',
            'mail_from_name'       => 'nullable|string',
            'mail_from_address'    => 'nullable|email',
            'booking_enquiry_email'=> 'nullable|email',
            'primary_color'        => 'nullable|string',
            'site_logo_type'       => 'nullable|in:text,image',
            'map_embed'            => 'nullable|string',
            'logo_image_id'        => 'nullable|exists:images,id',
        ]);

        $settingKeys = [
            'hotel_name', 'hotel_tagline', 'hotel_description',
            'hotel_email', 'hotel_phone', 'hotel_address', 'hotel_city', 'hotel_country',
            'currency', 'currency_symbol', 'tax_rate', 'check_in_time', 'check_out_time',
            'facebook_url', 'instagram_url', 'twitter_url', 'map_embed',
            'mail_host', 'mail_port', 'mail_username', 'mail_password',
            'mail_from_name', 'mail_from_address', 'booking_enquiry_email',
            'primary_color', 'site_logo_type',
        ];

        foreach ($settingKeys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        // Boolean toggles — checkboxes are absent when unchecked, so handle explicitly
        Setting::set('show_featured_room_price', $request->has('show_featured_room_price') ? '1' : '0');

        // Handle logo image from picker
        if ($request->filled('logo_image_id')) {
            $img = $this->images->find((int) $request->logo_image_id);
            if ($img) {
                Setting::set('logo_url', $img->url);
            }
        }

        Cache::flush();

        return back()->with('success', 'Settings saved successfully');
    }
}
