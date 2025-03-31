<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumPost;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::all();
        return view('forum.index', compact('categories'));
    }

    public function showCategory($id)
    {
        $category = ForumCategory::findOrFail($id);
        return view('forum.category', compact('category'));
    }

    public function showTopic($id)
    {
        $topic = ForumTopic::with('posts')->findOrFail($id);
        return view('forum.topic', compact('topic'));
    }

    public function storeTopic(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:forum_categories,id',
        ]);

        ForumTopic::create([
            'title' => strip_tags($validated['title']),
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
        ]);

        return redirect()->route('forum.index')->with('success', 'Temat dodany.');
    }

    public function storePost(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'topic_id' => 'required|exists:forum_topics,id',
        ]);

        ForumPost::create([
            'content' => strip_tags($validated['content']),
            'user_id' => auth()->id(),
            'topic_id' => $validated['topic_id'],
        ]);

        return redirect()->route('forum.topic', $validated['topic_id'])->with('success', 'Odpowiedź dodana.');
    }
}
