<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventAdminController extends Controller
{
    private ImageService $images;

    public function __construct(ImageService $images)
    {
        $this->images = $images;
    }

    public function index(Request $request)
    {
        $query = Event::query()->orderBy('starts_at', 'desc')->orderBy('sort_order');

        if ($request->filled('type') && array_key_exists($request->type, Event::TYPES)) {
            $query->byType($request->type);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('venue', 'like', "%{$s}%")
                  ->orWhere('organizer', 'like', "%{$s}%");
            });
        }

        $events = $query->paginate(15)->withQueryString();
        $types  = Event::TYPES;

        return view('admin.events.index', compact('events', 'types'));
    }

    public function create(Request $request)
    {
        $types        = Event::TYPES;
        $selectedType = array_key_exists($request->get('type'), $types) ? $request->get('type') : 'event';
        return view('admin.events.create', compact('types', 'selectedType'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if (empty($data['slug'])) {
            $data['slug'] = Event::uniqueSlug($data['title']);
        }

        if ($request->filled('image_id')) {
            $img = $this->images->find((int) $request->image_id);
            if ($img) {
                $data['image_url'] = $img->url;
            }
        }

        $event = Event::create($data);

        if ($request->filled('image_id')) {
            $this->images->attach($event, (int) $request->image_id, 'featured');
        }

        return redirect()->route('admin.events.index')
            ->with('success', $event->type_label . ' created');
    }

    public function edit(Event $event)
    {
        $types         = Event::TYPES;
        $featuredImage = $this->images->first($event, 'featured');
        return view('admin.events.edit', compact('event', 'types', 'featuredImage'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validated($request, $event->id);

        if (empty($data['slug'])) {
            $data['slug'] = $event->slug;
        }

        if ($request->filled('image_id')) {
            $img = $this->images->find((int) $request->image_id);
            if ($img) {
                $data['image_url'] = $img->url;
            }
            $this->images->attach($event, (int) $request->image_id, 'featured');
        }

        $event->update($data);

        return back()->with('success', $event->type_label . ' updated');
    }

    public function destroy(Event $event)
    {
        $this->images->detach($event);
        $label = $event->type_label;
        $event->delete();
        return back()->with('success', $label . ' deleted');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:events,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
            'type'        => 'required|in:' . implode(',', array_keys(Event::TYPES)),
            'excerpt'     => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'venue'       => 'nullable|string|max:255',
            'organizer'   => 'nullable|string|max:255',
            'capacity'    => 'nullable|integer|min:1',
            'price'       => 'nullable|numeric|min:0',
            'cta_text'    => 'nullable|string|max:100',
            'cta_url'     => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'image_id'    => 'nullable|exists:images,id',
        ]);

        unset($data['image_id']);
        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order']  = (int) ($data['sort_order'] ?? 0);
        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        return $data;
    }
}
