<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message $message
    ) {
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('conversation.'.$this->message->conversation_id),
        ];

        $recipientBusinessAccountId = $this->recipientBusinessAccountId();
        if ($recipientBusinessAccountId) {
            $channels[] = new PrivateChannel('business-account.'.$recipientBusinessAccountId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_business_account_id' => $this->message->sender_business_account_id,
            'sender_name' => $this->message->senderBusinessAccount?->getTranslation('name', app()->getLocale()),
            'body' => $this->message->body,
            'status' => $this->message->status,
            'read_at' => $this->message->read_at,
            'created_at' => $this->message->created_at,
        ];
    }

    protected function recipientBusinessAccountId(): ?int
    {
        $conversation = $this->message->conversation;
        if (! $conversation) {
            return null;
        }

        if ((int) $this->message->sender_business_account_id === (int) $conversation->initiator_business_account_id) {
            return (int) $conversation->recipient_business_account_id;
        }

        return (int) $conversation->initiator_business_account_id;
    }
}
