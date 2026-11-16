<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::with(['facility', 'user'])
            ->public()
            ->upcoming();

        // Filtrowanie po placówce
        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        // Filtrowanie po dacie
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        // Bez cache: Cache::flush() przy zmianach kasował też liczniki limitów logowania
        $events = $query->paginate(15);

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'facility_id' => 'nullable|exists:facilities,id',
            'is_public' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $event = Event::create($validated);


        return redirect()->route('events.show', $event)
            ->with('success', 'Wydarzenie zostało utworzone');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // Eager load relacji dla pojedynczego wydarzenia
        $event->load(['facility', 'user']);

        // Sprawdź, czy użytkownik może zobaczyć prywatne wydarzenie
        if (!$event->is_public && (!auth()->check() || auth()->id() !== $event->user_id)) {
            abort(403, 'Nie masz dostępu do tego wydarzenia');
        }

        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Tylko właściciel lub admin może edytować
        if (auth()->id() !== $event->user_id && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Tylko właściciel lub admin może edytować
        if (auth()->id() !== $event->user_id && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'facility_id' => 'nullable|exists:facilities,id',
            'is_public' => 'boolean',
        ]);

        $event->update($validated);


        return redirect()->route('events.show', $event)
            ->with('success', 'Wydarzenie zostało zaktualizowane');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Tylko właściciel lub admin może usunąć
        if (auth()->id() !== $event->user_id && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $event->delete();


        return redirect()->route('events.index')
            ->with('success', 'Wydarzenie zostało usunięte');
    }
}
