<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConferenceInquiry;
use Illuminate\Http\Request;

class ConferenceInquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = ConferenceInquiry::orderByDesc('created_at');

        if ($request->filled('status') && array_key_exists($request->status, ConferenceInquiry::STATUSES)) {
            $query->status($request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('company', 'like', "%{$s}%");
            });
        }

        $inquiries = $query->paginate(20)->withQueryString();
        $statuses  = ConferenceInquiry::STATUSES;

        return view('admin.conference.inquiries.index', compact('inquiries', 'statuses'));
    }

    public function show(ConferenceInquiry $inquiry)
    {
        $statuses = ConferenceInquiry::STATUSES;
        return view('admin.conference.inquiries.show', compact('inquiry', 'statuses'));
    }

    public function update(Request $request, ConferenceInquiry $inquiry)
    {
        $data = $request->validate([
            'status'      => 'required|in:' . implode(',', array_keys(ConferenceInquiry::STATUSES)),
            'admin_notes' => 'nullable|string',
        ]);

        $inquiry->update($data);

        return back()->with('success', 'Inquiry updated.');
    }

    public function destroy(ConferenceInquiry $inquiry)
    {
        $inquiry->delete();
        return redirect()->route('admin.conference-inquiries.index')->with('success', 'Inquiry deleted.');
    }
}
