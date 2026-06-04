<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\GuestNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::with('bookings');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->vip_status) {
            $query->where('vip_status', $request->vip_status);
        }

        $guests = $query->orderByDesc('created_at')->paginate(20);

        $vipCounts = [
            'regular' => Guest::where('vip_status', 'regular')->count(),
            'silver' => Guest::where('vip_status', 'silver')->count(),
            'gold' => Guest::where('vip_status', 'gold')->count(),
            'platinum' => Guest::where('vip_status', 'platinum')->count(),
        ];

        return view('admin.guests.index', compact('guests', 'vipCounts'));
    }

    public function show(Guest $guest)
    {
        $guest->load(['bookings.room', 'notes.user']);
        return view('admin.guests.show', compact('guest'));
    }

    public function edit(Guest $guest)
    {
        return view('admin.guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'nullable|string',
            'nationality' => 'nullable|string',
            'country' => 'nullable|string',
            'address' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'vip_status' => 'required|in:regular,silver,gold,platinum',
            'is_blacklisted' => 'boolean',
        ]);

        $guest->update($request->all());
        return redirect()->route('admin.guests.show', $guest)->with('success', 'Guest updated');
    }

    public function destroy(Guest $guest)
    {
        $guest->notes()->delete();
        $guest->delete();
        return redirect()->route('admin.guests.index')->with('success', 'Guest deleted');
    }

    public function addNote(Request $request, Guest $guest)
    {
        $request->validate([
            'note' => 'required|string',
            'type' => 'required|in:general,booking,complaint,compliment',
        ]);

        $guest->notes()->create([
            'user_id' => Auth::id(),
            'note' => $request->note,
            'type' => $request->type,
        ]);

        return back()->with('success', 'Note added');
    }
}
