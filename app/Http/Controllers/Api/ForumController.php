<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ForumTopicResource;
use App\Http\Resources\ForumPostResource;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    /**
     * Display a listing of forum topics.
     */
    public function topics(Request $request)
    {
        $query = ForumTopic::with(['user', 'category'])
            ->withCount('posts');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('forum_category_id', $request->category_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'most_replies':
                $query->orderBy('posts_count', 'desc');
                break;
            default:
                $query->orderBy('is_pinned', 'desc')
                      ->orderBy('created_at', 'desc');
        }

        $topics = $query->paginate(min(max((int) $request->get('per_page', 20), 1), 100));

        return ForumTopicResource::collection($topics);
    }

    /**
     * Display the specified topic with posts.
     */
    public function showTopic(ForumTopic $topic)
    {
        $topic->load(['user', 'category', 'posts.user']);
        $topic->increment('views_count');

        return new ForumTopicResource($topic);
    }

    /**
     * Store a newly created topic.
     */
    public function storeTopic(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'forum_category_id' => 'required|exists:forum_categories,id',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth('api')->id();

        $topic = ForumTopic::create($validated);
        $topic->load(['user', 'category']);

        return new ForumTopicResource($topic);
    }

    /**
     * Update the specified topic.
     */
    public function updateTopic(Request $request, ForumTopic $topic)
    {
        // Check if user owns the topic or is admin
        if ($topic->user_id !== auth('api')->id() && !auth('api')->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $topic->update($validated);

        return new ForumTopicResource($topic);
    }

    /**
     * Remove the specified topic.
     */
    public function destroyTopic(ForumTopic $topic)
    {
        // Check if user owns the topic or is admin
        if ($topic->user_id !== auth('api')->id() && !auth('api')->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $topic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topic deleted successfully'
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function storePost(Request $request, ForumTopic $topic)
    {
        if ($topic->is_locked && !auth('api')->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'This topic is locked'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $post = $topic->posts()->create([
            'content' => $validated['content'],
            'user_id' => auth('api')->id(),
        ]);

        $post->load('user');

        return new ForumPostResource($post);
    }

    /**
     * Update the specified post.
     */
    public function updatePost(Request $request, ForumPost $post)
    {
        // Check if user owns the post or is admin
        if ($post->user_id !== auth('api')->id() && !auth('api')->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $post->update($validated);

        return new ForumPostResource($post);
    }

    /**
     * Remove the specified post.
     */
    public function destroyPost(ForumPost $post)
    {
        // Check if user owns the post or is admin
        if ($post->user_id !== auth('api')->id() && !auth('api')->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }
}
