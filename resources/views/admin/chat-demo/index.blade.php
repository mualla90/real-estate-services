@extends('layouts.admin')

@section('content')
    <div class="container-fluid admin-page"
         data-chat-demo
         data-conversation-id="{{ $conversation?->id }}"
         data-auth-endpoint="{{ route('admin.chat-demo.pusher.auth') }}"
         data-send-endpoint="{{ $conversation ? route('admin.chat-demo.messages.store', $conversation) : '' }}"
         data-pusher-key="{{ $pusher['key'] }}"
         data-pusher-cluster="{{ $pusher['cluster'] }}"
         data-pusher-scheme="{{ $pusher['scheme'] }}"
         data-pusher-host="{{ $pusher['host'] }}"
         data-pusher-port="{{ $pusher['port'] }}"
         data-inbox-business-account-ids="{{ $businessAccounts->pluck('id')->implode(',') }}">
        <div class="admin-page-header mb-4">
            <div class="page-title-wrap">
                <h3 class="mb-0 page-title d-flex align-items-center gap-2">
                    <span class="page-title-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M4 5h16v10H8l-4 4V5Zm5 4h6M9 12h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span>Chat Demo</span>
                </h3>
                <div class="page-subtitle">Test business account conversations, live Pusher delivery, and inbox counters.</div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-4">
                <div class="card filter-card mb-4">
                    <div class="card-header">Create or Open Conversation</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.chat-demo.conversations.store') }}" class="vstack gap-3">
                            @csrf

                            <div>
                                <label class="form-label">Requester Business Account</label>
                                <select name="initiator_business_account_id" class="form-select" required>
                                    <option value="">Choose account</option>
                                    @foreach($businessAccounts as $businessAccount)
                                        <option value="{{ $businessAccount->id }}">
                                            #{{ $businessAccount->id }} {{ $businessAccount->getTranslation('name', app()->getLocale()) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Service</label>
                                <select name="service_id" class="form-select" required>
                                    <option value="">Choose service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">
                                            #{{ $service->id }} {{ $service->title }} - owner #{{ $service->business_account_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Open Conversation</button>
                        </form>
                    </div>
                </div>

                <div class="card table-card">
                    <div class="card-header">Recent Conversations</div>
                    <div class="list-group list-group-flush">
                        @forelse($conversations as $item)
                            <a href="{{ route('admin.chat-demo.index', ['conversation' => $item->id]) }}"
                               data-conversation-alert="{{ $item->id }}"
                               class="list-group-item list-group-item-action chat-conversation-item {{ $conversation?->id === $item->id ? 'active' : '' }}">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <span class="fw-semibold">Conversation #{{ $item->id }}</span>
                                    <span class="badge bg-danger d-none" data-conversation-alert-count="{{ $item->id }}">0</span>
                                </div>
                                <div class="small chat-conversation-parties">
                                    {{ $item->initiatorBusinessAccount?->getTranslation('name', app()->getLocale()) }}
                                    -> {{ $item->recipientBusinessAccount?->getTranslation('name', app()->getLocale()) }}
                                </div>
                                <div class="small text-muted">{{ optional($item->last_message_at)->diffForHumans() ?: 'No messages yet' }}</div>
                            </a>
                        @empty
                            <div class="list-group-item text-muted">No conversations yet.</div>
                        @endforelse
                    </div>
                </div>

                <div class="card table-card mt-4">
                    <div class="card-header">Live Inbox Counters</div>
                    <div class="list-group list-group-flush">
                        @foreach($businessAccounts as $businessAccount)
                            <div class="list-group-item d-flex justify-content-between align-items-center gap-2 chat-inbox-row">
                                <span>
                                    <span class="chat-account-avatar">{{ mb_substr($businessAccount->getTranslation('name', app()->getLocale()), 0, 1) }}</span>
                                    #{{ $businessAccount->id }} {{ $businessAccount->getTranslation('name', app()->getLocale()) }}
                                </span>
                                <span class="badge bg-primary d-none"
                                      data-business-account-inbox-count="{{ $businessAccount->id }}">0</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                @if($conversation)
                    <div class="card table-card chat-panel">
                        <div class="card-header chat-panel-header">
                            <div>
                                <div class="fw-semibold">Conversation #{{ $conversation->id }}</div>
                                <div class="text-muted small">{{ $conversation->service?->title }}</div>
                            </div>
                            <div class="chat-participant-pair">
                                <span>{{ $conversation->initiatorBusinessAccount?->getTranslation('name', app()->getLocale()) }}</span>
                                <span class="chat-participant-divider">/</span>
                                <span>{{ $conversation->recipientBusinessAccount?->getTranslation('name', app()->getLocale()) }}</span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="chat-demo-log"
                                 data-chat-messages
                                 data-current-business-account-id="{{ $conversation->initiator_business_account_id }}">
                                @foreach($messages as $message)
                                    <div class="chat-demo-message {{ (int) $message->sender_business_account_id === (int) $conversation->initiator_business_account_id ? 'is-outgoing' : 'is-incoming' }}"
                                         data-message-id="{{ $message->id }}"
                                         data-sender-business-account-id="{{ $message->sender_business_account_id }}">
                                        <div class="chat-demo-bubble">
                                            <div class="chat-demo-meta">
                                                {{ $message->senderBusinessAccount?->getTranslation('name', app()->getLocale()) }}
                                                <span>#{{ $message->sender_business_account_id }}</span>
                                            </div>
                                            <div class="chat-demo-text">{{ $message->body }}</div>
                                            <div class="chat-demo-time">{{ optional($message->created_at)->format('H:i') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row g-3">
                                @foreach([$conversation->initiatorBusinessAccount, $conversation->recipientBusinessAccount] as $participant)
                                    @if($participant)
                                        <div class="col-lg-6">
                                            <form data-chat-send-form class="vstack gap-2">
                                                <input type="hidden" name="sender_business_account_id" value="{{ $participant->id }}">
                                                <label class="form-label mb-0">
                                                    Send as {{ $participant->getTranslation('name', app()->getLocale()) }} #{{ $participant->id }}
                                                </label>
                                                <textarea name="body" class="form-control" rows="3" required maxlength="2000"></textarea>
                                                <button type="submit" class="btn btn-primary">Send</button>
                                            </form>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card table-card">
                        <div class="card-body text-muted">
                            Create a conversation from an approved business account and an approved visible service.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
