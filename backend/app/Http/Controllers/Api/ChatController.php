<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Get customer chat data for support
     */
    public function index()
    {
        // Get all customers with active conversations
        $customers = User::where('role', 'customer')
            ->whereHas('sentMessages', function ($query) {
                $query->whereIn('receiver_id', User::where('role', 'admin')->pluck('id'))
                    ->orWhereIn('sender_id', User::where('role', 'admin')->pluck('id'));
            })
            ->with(['sentMessages' => function ($query) {
                $query->orderBy('created_at', 'desc')->first();
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    /**
     * Get customer chat interface data
     */
    public function customerChat()
    {
        $support = User::where('role', 'admin')->first();

        if (!$support) {
            return response()->json([
                'success' => false,
                'message' => 'No admin available to assist you.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'support' => $support
            ]
        ]);
    }

    /**
     * Fetch messages for a specific conversation
     */
    public function fetchMessages($receiverId)
    {
        $receiver = User::find($receiverId);

        if (!$receiver) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $isSenderCustomer = Auth::user()->role === 'customer';

        if ($isSenderCustomer && $receiver->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Customers can only message admins.'
            ], 403);
        }

        if (!$isSenderCustomer && $receiver->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Admins can only message customers.'
            ], 403);
        }

        $messages = Message::where(function ($query) use ($receiverId) {
            $query->where('sender_id', Auth::id())->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        // Mark messages as read
        Message::where('sender_id', $receiverId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $formattedMessages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'is_sent' => $message->sender_id === Auth::id(),
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at,
                'formatted_time' => $message->created_at->format('H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedMessages,
            'receiver' => $receiver
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $receiver = User::find($request->receiver_id);
        $isSenderCustomer = Auth::user()->role === 'customer';
        $isReceiverAdmin = $receiver->role === 'admin';
        $isReceiverCustomer = $receiver->role === 'customer';

        if ($isSenderCustomer && !$isReceiverAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Customers can only message admins.'
            ], 403);
        }

        if (!$isSenderCustomer && !$isReceiverCustomer) {
            return response()->json([
                'success' => false,
                'message' => 'Admins can only message customers.'
            ], 403);
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Broadcast the message (if using broadcasting)
        try {
            broadcast(new MessageSent(
                $message->sender_id,
                $message->receiver_id,
                $message->message,
                $message->created_at
            ))->toOthers();
        } catch (\Exception $e) {
            // Broadcasting failed, but message was still saved
            \Log::warning('Failed to broadcast message: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'is_sent' => true,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at,
                'formatted_time' => $message->created_at->format('H:i')
            ]
        ], 201);
    }

    /**
     * Get unread message count
     */
    public function unreadCount()
    {
        $unreadCount = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        Message::where('sender_id', $request->sender_id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read'
        ]);
    }
}
