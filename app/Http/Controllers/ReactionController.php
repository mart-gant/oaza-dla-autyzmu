<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reaction;

class ReactionController extends Controller
{
    public function index()
    {
        $reactions = Reaction::with('user')->latest()->get();
        
        return view('reactions.index', compact('reactions'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'reactionable_id' => 'required|integer',
            'reactionable_type' => 'required|string',
            'type' => 'required|string'
        ]);
        
        $reaction = Reaction::create([
            'user_id' => auth()->id(),
            'reactionable_id' => $request->reactionable_id,
            'reactionable_type' => $request->reactionable_type,
            'type' => $request->type
        ]);
        
        if ($request->ajax()) {
            return response()->json(['message' => 'Reakcja dodana!', 'reaction' => $reaction]);
        }
        
        return back()->with('success', 'Reakcja dodana!');
    }
    
    public function destroy(Reaction $reaction)
    {
        if ($reaction->user_id == auth()->id() || auth()->user()->isAdmin()) {
            $reaction->delete();
            return response()->json(['message' => 'Reakcja usunięta!']);
        }
        
        return response()->json(['error' => 'Brak uprawnień!'], 403);
    }
}
