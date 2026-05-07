<?php

namespace App\Services\Chat;

use App\Models\BusinessAccount;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ChatService
{
    public function createConversation(BusinessAccount $businessAccount, int $serviceId): Conversation
    {
        $this->ensureBusinessAccountApproved($businessAccount);

        $service = Service::query()->findOrFail($serviceId);
        abort_unless($service->isVisible(), 422, __('api.errors.service_unavailable_for_chat'));

        $recipientBusinessAccountId = $service->business_account_id;
        abort_if($recipientBusinessAccountId === $businessAccount->id, 422, __('api.errors.cannot_chat_with_self'));

        $conversation = Conversation::query()
            ->where('service_id', $service->id)
            ->where(function ($q) use ($businessAccount, $recipientBusinessAccountId) {
                $q->where(function ($sub) use ($businessAccount, $recipientBusinessAccountId) {
                    $sub->where('initiator_business_account_id', $businessAccount->id)
                        ->where('recipient_business_account_id', $recipientBusinessAccountId);
                })->orWhere(function ($sub) use ($businessAccount, $recipientBusinessAccountId) {
                    $sub->where('initiator_business_account_id', $recipientBusinessAccountId)
                        ->where('recipient_business_account_id', $businessAccount->id);
                });
            })
            ->first();

        if ($conversation) {
            return $conversation->load($this->conversationRelations());
        }

        return DB::transaction(function () use ($service, $businessAccount, $recipientBusinessAccountId) {
            return Conversation::query()->create([
                'service_id' => $service->id,
                'initiator_business_account_id' => $businessAccount->id,
                'recipient_business_account_id' => $recipientBusinessAccountId,
                'last_message_at' => null,
            ])->load($this->conversationRelations());
        });
    }

    public function listConversations(BusinessAccount $businessAccount, int $perPage = 15): LengthAwarePaginator
    {
        return Conversation::query()
            ->with($this->conversationRelations())
            ->where(function ($q) use ($businessAccount) {
                $q->where('initiator_business_account_id', $businessAccount->id)
                    ->orWhere('recipient_business_account_id', $businessAccount->id);
            })
            ->withCount([
                'messages as unread_messages_count' => function ($q) use ($businessAccount) {
                    $q->where('status', 'sent')
                        ->where('sender_business_account_id', '!=', $businessAccount->id);
                },
            ])
            ->orderByDesc('last_message_at')
            ->latest('id')
            ->paginate($perPage);
    }

    public function listMessages(BusinessAccount $businessAccount, Conversation $conversation, int $perPage = 30): LengthAwarePaginator
    {
        $this->ensureParticipant($businessAccount, $conversation);

        return $conversation->messages()
            ->with('senderBusinessAccount')
            ->oldest('id')
            ->paginate($perPage);
    }

    public function sendMessage(BusinessAccount $businessAccount, Conversation $conversation, string $body): Message
    {
        $this->ensureParticipant($businessAccount, $conversation);

        return DB::transaction(function () use ($businessAccount, $conversation, $body) {
            $message = Message::query()->create([
                'conversation_id' => $conversation->id,
                'sender_business_account_id' => $businessAccount->id,
                'body' => $body,
                'status' => 'sent',
                'read_at' => null,
            ]);

            $conversation->update([
                'last_message_at' => now(),
            ]);

            return $message->load('senderBusinessAccount', 'conversation');
        });
    }

    public function markConversationRead(BusinessAccount $businessAccount, Conversation $conversation): int
    {
        $this->ensureParticipant($businessAccount, $conversation);

        return Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_business_account_id', '!=', $businessAccount->id)
            ->where('status', 'sent')
            ->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
    }

    public function ensureParticipant(BusinessAccount $businessAccount, Conversation $conversation): void
    {
        abort_if(
            $conversation->initiator_business_account_id !== $businessAccount->id
            && $conversation->recipient_business_account_id !== $businessAccount->id,
            403,
            __('api.errors.unauthorized')
        );
    }

    public function ensureBusinessAccountApproved(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->status !== 'approved', 422, __('api.errors.business_account_not_approved'));
    }

    protected function conversationRelations(): array
    {
        return [
            'service.category',
            'service.subcategory',
            'service.city',
            'service.media',
            'initiatorBusinessAccount',
            'recipientBusinessAccount',
            'latestMessage.senderBusinessAccount',
        ];
    }
}
