<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $settings = [
            'hotel_address' => Setting::get('hotel_address'),
            'hotel_phone'   => Setting::get('hotel_phone'),
            'hotel_email'   => Setting::get('hotel_email'),
            'hotel_name'    => Setting::get('hotel_name', 'Bellevie Hotel'),
        ];

        // Load builder sections for the contact page (saved via Admin → Contact Page Builder)
        $raw      = Setting::get('contact_builder_data', '');
        $sections = $raw ? (json_decode($raw, true) ?? []) : [];

        return view('frontend.contact', compact('settings', 'sections'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'name'    => 'required|string',
            'email'   => 'required|email',
            'phone'   => 'required|string',
            'subject' => 'required|string',
            'message' => 'required|string|min:10',
            'g-recaptcha-response' => [new Recaptcha()],
        ]);

        // TODO: send email or store to DB

        return back()->with('contact_success', 'Thank you for contacting us. We will respond shortly.');
    }
}
