<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Conversation;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminChatController extends Controller
{
    public function index()
    {
        // Get all conversations for admin
        $conversations = Conversation::with(['latestMessage', 'customer'])
            ->withCount(['messages as unread_count' => function($query) {
                $query->where('is_read', false)
                      ->where('sender_type', 'customer');
            }])
            ->orderBy('unread_count', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json(
            $conversations->map(function($conversation) {
                return [
                    'id' => $conversation->id,
                    'customer_id' => $conversation->customer_id,
                    'customer_name' => $conversation->customer->name,
                    'customer_email' => $conversation->customer->email,
                    'unread_count' => $conversation->unread_count,
                    'last_message' => $conversation->latestMessage ? [
                        'message' => $conversation->latestMessage->message,
                        'sender_type' => $conversation->latestMessage->sender_type,
                        'created_at' => $conversation->latestMessage->created_at,
                    ] : null,
                    'is_online' => false, // This will be updated via Pusher presence channel
                    'updated_at' => $conversation->updated_at,
                ];
            })
        );
    }

    public function messages(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        // Get messages with pagination
        $messages = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Mark all customer messages as read
        $conversation->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(
            $messages->map(function($message) {
                return [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'sender_id' => $message->sender_id,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at,
                ];
            })
        );
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $conversation = Conversation::findOrFail($conversationId);

        DB::beginTransaction();
        try {
            $message = $conversation->messages()->create([
                'message' => $request->message,
                'sender_type' => 'admin',
                'sender_id' => Auth::id(),
                'is_read' => false,
            ]);

            $conversation->touch();

            // Broadcast the message
            broadcast(new MessageSent(
                $conversation,
                $message,
                Auth::user(),
                'admin'
            ))->toOthers();

            DB::commit();

            return response()->json([
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'sender_id' => $message->sender_id,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function markRead(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        $conversation->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'Messages marked as read'
        ]);
    }
}
