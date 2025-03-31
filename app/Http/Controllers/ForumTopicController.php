<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumTopic;
//use App\Models\Category; // Dodaj model kategorii
use Illuminate\Support\Facades\Auth;

class ForumTopicController extends Controller
{
    public function index(Request $request)
    {
        // Walidacja danych wejściowych
        $search = $request->validate([
            'search' => 'nullable|string|max:255'
        ])['search'] ?? null;
        
        // Pobieranie tematów forum z opcją wyszukiwania
        $topics = ForumTopic::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%$search%");
        })
        ->paginate(10);

        // Pobranie kategorii z bazy
        $categories = ForumCategory::all();
        
        return view('forum.index', compact('topics', 'search', 'categories')); // Przekazanie kategorii do widoku
    }
    
    public function destroy(ForumTopic $topic)
    {
        // Sprawdzenie uprawnień użytkownika
        if (!Auth::user()->can('delete', $topic)) {
            return redirect()->route('forum.index')->with('error', 'Brak uprawnień.');
        }
        
        $topic->delete();
        return redirect()->route('forum.index')->with('success', 'Temat usunięty.');
    }
}
