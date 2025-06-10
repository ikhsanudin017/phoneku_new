<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function index()
    {
        // Get all users who have chats, ordered by latest message
        $users = User::whereHas('messages')
            ->with(['messages' => function($query) {
                $query->latest()->first();
            }])
            ->withCount(['unreadMessages' => function($query) {
                $query->where('is_read', false)
                    ->where('is_from_user', true);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function messages(User $user)
    {
        $messages = Message::where(function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('created_at', 'desc')
          ->paginate(50);

        // Mark messages as read
        Message::where('user_id', $user->id)
            ->where('is_from_user', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function sendMessage(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'is_from_user' => false,
            'is_read' => false
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message
        ]);
    }
}
