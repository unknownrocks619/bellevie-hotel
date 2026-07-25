<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::active()->orderBy('starts_at')->orderBy('sort_order');

        $type = $request->get('type');
        if ($type && array_key_exists($type, Event::TYPES)) {
            $query->byType($type);
        }

        $events = $query->paginate(9)->withQueryString();
        $types  = Event::TYPES;

        return view('frontend.events.index', compact('events', 'types', 'type'));
    }

    public function show(Event $event)
    {
        abort_unless($event->is_active, 404);

        $related = Event::active()
            ->where('id', '!=', $event->id)
            ->byType($event->type)
            ->upcoming()
            ->orderBy('starts_at')
            ->take(3)
            ->get();

        return view('frontend.events.show', compact('event', 'related'));
    }
}
