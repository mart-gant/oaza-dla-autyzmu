<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // Pobranie wartości z zapytania GET i walidacja
        $search = $request->validate([
            'search' => 'nullable|string|max:255'
        ])['search'] ?? null;
        
        // Pobieranie artykułów z filtrowaniem, jeśli podano wyszukiwaną frazę
        $articles = Article::with('user')
            ->where('is_published', true)
            ->when($search, function ($query, $search) {
                // Nawias w SQL: bez niego orWhere omija warunek is_published
                return $query->where(fn ($q) => $q->where('title', 'like', "%$search%")
                    ->orWhere('content', 'like', "%$search%"));
            })
            ->orderBy('published_at', 'desc')
            ->paginate(10);
        
        // Przekazanie wyników do widoku
        return view('articles.index', compact('articles', 'search'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        
        if (isset($validated['is_published']) && $validated['is_published']) {
            $validated['published_at'] = now();
        }

        $article = Article::create($validated);

        return redirect()->route('articles.show', $article->id)
            ->with('success', 'Artykuł został utworzony pomyślnie!');
    }

    public function show(Article $article)
    {
        // Sprawdź czy artykuł jest opublikowany lub czy użytkownik jest autorem
        if (!$article->is_published && (!auth()->check() || auth()->id() !== $article->user_id)) {
            abort(404);
        }

        $article->load('user');
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        // Sprawdź czy użytkownik jest autorem lub adminem
        if (auth()->id() !== $article->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Nie masz uprawnień do edycji tego artykułu.');
        }

        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        // Sprawdź czy użytkownik jest autorem lub adminem
        if (auth()->id() !== $article->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Nie masz uprawnień do edycji tego artykułu.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . $article->id;
        
        // Jeśli artykuł jest publikowany po raz pierwszy
        if (isset($validated['is_published']) && $validated['is_published'] && !$article->is_published) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('articles.show', $article->id)
            ->with('success', 'Artykuł został zaktualizowany pomyślnie!');
    }

    public function destroy(Article $article)
    {
        // Sprawdź czy użytkownik jest autorem lub adminem
        if (auth()->id() !== $article->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Nie masz uprawnień do usunięcia tego artykułu.');
        }

        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Artykuł został usunięty pomyślnie!');
    }
}
