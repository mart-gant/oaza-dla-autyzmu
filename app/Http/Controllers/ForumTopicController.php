<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumTopic;
use App\Models\ForumCategory;
use Illuminate\Support\Facades\Auth;

class ForumTopicController extends Controller
{
    public function categories()
    {
        $categories = ForumCategory::all();
        return view('forum.categories', compact('categories'));
    }

    public function index(Request $request, ForumCategory $category)
    {
        $search = $request->validate([
            'search' => 'nullable|string|max:255'
        ])['search'] ?? null;
        
        $topics = $category->topics()->when($search, function ($query, $search) {
            return $query->where('title', 'like', "%$search%");
        })
        ->paginate(10);

        return view('forum.index', compact('topics', 'category', 'search'));
    }

    public function show(ForumTopic $topic)
    {
        $posts = $topic->posts()->paginate(10);
        return view('forum.show', compact('topic', 'posts'));
    }

    public function create()
    {
        $categories = ForumCategory::all();
        return view('forum.create_topic', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'forum_category_id' => 'required|exists:forum_categories,id',
        ]);

        $topic = ForumTopic::create([
            'title' => $request->title,
            'user_id' => Auth::id(),
            'forum_category_id' => $request->forum_category_id,
        ]);

        $topic->posts()->create([
            'body' => $request->body,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('forum.show', $topic)->with('success', 'Temat został pomyślnie utworzony.');
    }

    public function edit(ForumTopic $topic)
    {
        $this->authorize('update', $topic);
        return view('forum.edit_topic', compact('topic'));
    }

    public function update(Request $request, ForumTopic $topic)
    {
        $this->authorize('update', $topic);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $topic->update([
            'title' => $request->title,
        ]);

        return redirect()->route('forum.show', $topic)->with('success', 'Temat został pomyślnie zaktualizowany.');
    }
    
    public function destroy(ForumTopic $topic)
    {
        $this->authorize('delete', $topic);
        
        $topic->delete();
        return redirect()->route('forum.index', $topic->category)->with('success', 'Temat usunięty.');
    }
}
