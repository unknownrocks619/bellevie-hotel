<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ConferenceInquiry;
use App\Models\ConferencePage;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function index()
    {
        $page = ConferencePage::singleton();
        abort_unless($page->is_active, 404);

        $galleryImages = $page->galleryImages();

        return view('frontend.conference.index', compact('page', 'galleryImages'));
    }

    public function storeInquiry(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'company'      => 'nullable|string|max:255',
            'event_date'   => 'nullable|date',
            'guests_count' => 'nullable|integer|min:1',
            'message'      => 'required|string|min:10',
        ]);

        ConferenceInquiry::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'company'      => $request->company,
            'event_date'   => $request->event_date,
            'guests_count' => $request->guests_count,
            'message'      => $request->message,
            'status'       => 'new',
        ]);

        return back()->with('conference_success', 'Thank you for your inquiry. Our events team will contact you shortly.');
    }
}
