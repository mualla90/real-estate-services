<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:api']]);

Broadcast::channel('conversation.{conversationId}', function ($user, int $conversationId) {
    $conversation = Conversation::query()->find($conversationId);

    if (! $conversation) {
        return false;
    }

    return $user->businessAccounts()
        ->whereIn('id', [
            $conversation->initiator_business_account_id,
            $conversation->recipient_business_account_id,
        ])
        ->exists();
});
