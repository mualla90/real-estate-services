<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\BusinessAccount;
use App\Models\Conversation;
use App\Models\Service;
use App\Services\Chat\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Pusher\Pusher;

class ChatDemoController extends Controller
{
    public function __construct(
        protected ChatService $chat
    ) {
    }

    public function index(Request $request): View
    {
        $conversation = $this->selectedConversation($request);

        return view('admin.chat-demo.index', [
            'businessAccounts' => BusinessAccount::query()
                ->where('status', 'approved')
                ->orderBy('id')
                ->get(),
            'services' => Service::query()
                ->with('businessAccount')
                ->where('status', 'approved')
                ->where('is_active', true)
                ->latest('id')
                ->get(),
            'conversations' => Conversation::query()
                ->with(['service', 'initiatorBusinessAccount', 'recipientBusinessAccount'])
                ->orderByDesc('last_message_at')
                ->latest('id')
                ->limit(50)
                ->get(),
            'conversation' => $conversation,
            'messages' => $conversation
                ? $conversation->messages()->with('senderBusinessAccount')->oldest('id')->get()
                : collect(),
            'pusher' => [
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'scheme' => config('broadcasting.connections.pusher.options.scheme', 'https'),
                'host' => config('broadcasting.connections.pusher.options.host'),
                'port' => config('broadcasting.connections.pusher.options.port', 443),
            ],
        ]);
    }

    public function storeConversation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'initiator_business_account_id' => ['required', 'integer', 'exists:business_accounts,id'],
        ]);

        $businessAccount = BusinessAccount::query()->findOrFail($validated['initiator_business_account_id']);
        $conversation = $this->chat->createConversation($businessAccount, (int) $validated['service_id']);

        return redirect()
            ->route('admin.chat-demo.index', ['conversation' => $conversation->id])
            ->with('success', 'Chat demo conversation is ready.');
    }

    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'sender_business_account_id' => ['required', 'integer', 'exists:business_accounts,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $sender = BusinessAccount::query()->findOrFail($validated['sender_business_account_id']);
        $message = $this->chat->sendMessage($sender, $conversation, $validated['body']);

        event(new MessageSent($message));

        return response()->json([
            'message' => __('api.chat.message_sent'),
            'data' => $this->messagePayload($message),
        ], 201);
    }

    public function pusherAuth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'socket_id' => ['required', 'string'],
            'channel_name' => ['required', 'string'],
        ]);

        $this->authorizeDemoChannel($validated['channel_name']);

        $pusher = new Pusher(
            (string) config('broadcasting.connections.pusher.key'),
            (string) config('broadcasting.connections.pusher.secret'),
            (string) config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        return response()->json(json_decode(
            $pusher->authorizeChannel($validated['channel_name'], $validated['socket_id']),
            true
        ));
    }

    protected function selectedConversation(Request $request): ?Conversation
    {
        $query = Conversation::query()
            ->with(['service', 'initiatorBusinessAccount', 'recipientBusinessAccount']);

        if ($request->filled('conversation')) {
            return $query->find($request->integer('conversation'));
        }

        return $query->orderByDesc('last_message_at')->latest('id')->first();
    }

    protected function authorizeDemoChannel(string $channelName): void
    {
        if (preg_match('/^private-conversation\.(\d+)$/', $channelName, $matches) === 1) {
            abort_unless(Conversation::query()->whereKey((int) $matches[1])->exists(), 403, __('admin.unauthorized'));

            return;
        }

        if (preg_match('/^private-business-account\.(\d+)$/', $channelName, $matches) === 1) {
            abort_unless(BusinessAccount::query()->whereKey((int) $matches[1])->exists(), 403, __('admin.unauthorized'));

            return;
        }

        abort(403, __('admin.unauthorized'));
    }

    protected function messagePayload($message): array
    {
        return [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_business_account_id' => $message->sender_business_account_id,
            'sender_name' => $message->senderBusinessAccount?->getTranslation('name', app()->getLocale()),
            'body' => $message->body,
            'status' => $message->status,
            'read_at' => $message->read_at,
            'created_at' => $message->created_at,
        ];
    }
}
