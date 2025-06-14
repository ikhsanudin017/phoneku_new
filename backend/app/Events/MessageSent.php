<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $message;
    public $sender;
    public $senderType;

    public function __construct($conversation, $message, $sender, $senderType)
    {
        $this->conversation = $conversation;
        $this->message = $message;
        $this->sender = $sender;
        $this->senderType = $senderType;
    }

    public function broadcastOn()
    {
        $channels = [];

        // Add customer's private channel
        $channels[] = new PrivateChannel('private-customer.' . $this->conversation->customer_id);

        // Add admin channel for all admins
        $channels[] = new PrivateChannel('private-admin.chat');

        return $channels;
    }

    public function broadcastAs()
    {
        return 'chat.message';
    }

    public function broadcastWith()
    {
        return [
            'conversation_id' => $this->conversation->id,
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'sender_type' => $this->senderType,
                'sender_id' => $this->sender->id,
                'sender_name' => $this->sender->name,
                'created_at' => $this->message->created_at,
                'is_read' => false
            ],
        ];
    }
}
