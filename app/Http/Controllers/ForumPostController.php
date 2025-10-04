<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use Illuminate\Support\Facades\Auth;

class ForumPostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'body' => 'required|string',
            'forum_topic_id' => 'required|exists:forum_topics,id',
        ]);

        ForumPost::create([
            'body' => $request->body,
            'user_id' => Auth::id(),
            'forum_topic_id' => $request->forum_topic_id,
        ]);

        return redirect()->back()->with('success', 'Odpowiedź została dodana.');
    }

    public function edit(ForumPost $post)
    {
        $this->authorize('update', $post);
        return view('forum.edit_post', compact('post'));
    }

    public function update(Request $request, ForumPost $post)
    {
        $this->authorize('update', $post);
        
        $request->validate([
            'body' => 'required|string',
        ]);

        $post->update([
            'body' => $request->body,
        ]);

        return redirect()->route('forum.show', $post->topic)->with('success', 'Post został pomyślnie zaktualizowany.');
    }

    public function destroy(ForumPost $post)
    {
        $this->authorize('delete', $post);

        $post->delete();
        return redirect()->back()->with('success', 'Post usunięty.');
    }

}
