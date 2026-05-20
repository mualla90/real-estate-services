<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Chat\StoreConversationRequest;
use App\Http\Requests\Api\Chat\StoreMessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\BusinessAccount;
use App\Models\Conversation;
use App\Services\Chat\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(
        protected ChatService $service
    ) {
    }

    public function index(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->ensureOwnership($businessAccount);
        $this->service->ensureBusinessAccountApproved($businessAccount);

        $perPage = (int) $request->input('per_page', 15);
        $conversations = $this->service->listConversations($businessAccount, $perPage);

        return response()->json([
            'message' => __('api.chat.conversations_fetched'),
            'data' => ConversationResource::collection($conversations),
        ]);
    }

    public function store(StoreConversationRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $conversation = $this->service->createConversation(
            $businessAccount,
            (int) $request->validated('service_id')
        );

        return response()->json([
            'message' => __('api.chat.conversation_created'),
            'data' => new ConversationResource($conversation),
        ], 201);
    }

    public function messages(Request $request, BusinessAccount $businessAccount, Conversation $conversation): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $perPage = (int) $request->input('per_page', 30);
        $messages = $this->service->listMessages($businessAccount, $conversation, $perPage);

        return response()->json([
            'message' => __('api.chat.messages_fetched'),
            'data' => MessageResource::collection($messages),
        ]);
    }

    public function sendMessage(
        StoreMessageRequest $request,
        BusinessAccount $businessAccount,
        Conversation $conversation
    ): JsonResponse {
        $this->ensureOwnership($businessAccount);

        $message = $this->service->sendMessage(
            $businessAccount,
            $conversation,
            $request->validated('body')
        );

        event(new MessageSent($message));

        return response()->json([
            'message' => __('api.chat.message_sent'),
            'data' => new MessageResource($message),
        ], 201);
    }

    public function markRead(BusinessAccount $businessAccount, Conversation $conversation): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $updated = $this->service->markConversationRead($businessAccount, $conversation);

        return response()->json([
            'message' => __('api.chat.messages_marked_read'),
            'data' => [
                'updated_count' => $updated,
            ],
        ]);
    }

    protected function ensureOwnership(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->user_id !== auth('api')->id(), 403, __('api.errors.unauthorized'));
    }
}
