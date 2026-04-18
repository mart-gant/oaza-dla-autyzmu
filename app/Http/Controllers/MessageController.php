<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display user's inbox with all conversations
     */
    public function index()
    {
        $userId = Auth::id();
        
        $conversations = Conversation::where('user_1_id', $userId)
            ->orWhere('user_2_id', $userId)
            ->with(['user1', 'user2', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        // Count unread messages
        $unreadCount = Message::unreadFor($userId)->count();

        return view('messages.index', compact('conversations', 'unreadCount'));
    }

    /**
     * Show a specific conversation
     */
    public function show(Conversation $conversation)
    {
        $userId = Auth::id();

        // Verify user is part of the conversation
        if (!$conversation->hasUser($userId)) {
            abort(403, 'Unauthorized access to this conversation');
        }

        // Load messages with pagination
        $messages = $conversation->messages()
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Mark messages as read
        $conversation->messages()
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $otherUser = $conversation->getOtherUser($userId);

        return view('messages.show', compact('conversation', 'messages', 'otherUser'));
    }

    /**
     * Show form to start new conversation
     */
    public function create(Request $request)
    {
        $recipientId = $request->query('to');
        $recipient = null;

        if ($recipientId) {
            $recipient = User::findOrFail($recipientId);
            
            // Check if conversation already exists
            $existing = Conversation::between(Auth::id(), $recipientId);
            if ($existing->exists) {
                return redirect()->route('messages.show', $existing);
            }
        }

        return view('messages.create', compact('recipient'));
    }

    /**
     * Send a new message
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:5000',
        ]);

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;

        // Prevent sending messages to yourself
        if ($senderId === (int)$receiverId) {
            return back()->withErrors(['receiver_id' => 'You cannot send messages to yourself.']);
        }

        // Get or create conversation
        $conversation = Conversation::between($senderId, $receiverId);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'content' => $request->content,
        ]);

        // Update conversation timestamp
        $conversation->update(['last_message_at' => now()]);

        // Send notification to receiver
        $receiver = User::find($receiverId);
        $receiver->notify(new NewMessage($message));

        return redirect()->route('messages.show', $conversation)
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Message $message)
    {
        // Verify user is the receiver
        if ($message->receiver_id !== Auth::id()) {
            abort(403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Get unread message count
     */
    public function unreadCount()
    {
        $count = Message::unreadFor(Auth::id())->count();
        
        return response()->json(['count' => $count]);
    }
}
