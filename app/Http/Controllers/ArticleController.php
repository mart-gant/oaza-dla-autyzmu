<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // Pobranie wartości z zapytania GET i walidacja
        $search = $request->validate([
            'search' => 'nullable|string|max:255'
        ])['search'] ?? null;
        
        // Pobieranie artykułów z filtrowaniem, jeśli podano wyszukiwaną frazę
        $articles = Article::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%$search%")
            ->orWhere('content', 'like', "%$search%");
        })
        ->paginate(10);
        
        // Przekazanie wyników do widoku
        return view('articles.index', compact('articles', 'search'));
    }
}
