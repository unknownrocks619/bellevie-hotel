<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index()
    {
        $amenities = Amenity::withCount('rooms')->paginate(20);
        return view('admin.amenities.index', compact('amenities'));
    }

    public function create()
    {
        return view('admin.amenities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:amenities',
            'icon' => 'nullable|string',
        ]);

        Amenity::create([
            'name'      => $request->name,
            'icon'      => $request->icon,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity created successfully.');
    }

    public function edit(Amenity $amenity)
    {
        return view('admin.amenities.edit', compact('amenity'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        $request->validate([
            'name' => 'required|string|unique:amenities,name,' . $amenity->id,
            'icon' => 'nullable|string',
        ]);

        $amenity->update([
            'name'      => $request->name,
            'icon'      => $request->icon,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity updated successfully.');
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->rooms()->detach();
        $amenity->delete();
        return redirect()->route('admin.amenities.index')->with('success', 'Amenity deleted successfully.');
    }
}
