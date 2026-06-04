<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::withCount('rooms')->paginate(20);
        return view('admin.room-types.index', compact('roomTypes'));
    }

    public function create()
    {
        return view('admin.room-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|unique:room_types',
            'description'  => 'nullable|string',
            'max_adults'   => 'required|integer|min:1',
            'max_children' => 'required|integer|min:0',
            'icon'         => 'nullable|string',
        ]);

        RoomType::create([
            'name'         => $request->name,
            'slug'         => Str::slug($request->name),
            'description'  => $request->description,
            'max_adults'   => $request->max_adults,
            'max_children' => $request->max_children,
            'icon'         => $request->icon,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.room-types.index')->with('success', 'Room type created successfully.');
    }

    public function edit(RoomType $roomType)
    {
        return view('admin.room-types.edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name'         => 'required|string|unique:room_types,name,' . $roomType->id,
            'description'  => 'nullable|string',
            'max_adults'   => 'required|integer|min:1',
            'max_children' => 'required|integer|min:0',
            'icon'         => 'nullable|string',
        ]);

        $roomType->update([
            'name'         => $request->name,
            'slug'         => Str::slug($request->name),
            'description'  => $request->description,
            'max_adults'   => $request->max_adults,
            'max_children' => $request->max_children,
            'icon'         => $request->icon,
            'is_active'    => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.room-types.index')->with('success', 'Room type updated successfully.');
    }

    public function destroy(RoomType $roomType)
    {
        if ($roomType->rooms()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete room type that has rooms assigned to it.']);
        }
        $roomType->delete();
        return redirect()->route('admin.room-types.index')->with('success', 'Room type deleted successfully.');
    }
}
