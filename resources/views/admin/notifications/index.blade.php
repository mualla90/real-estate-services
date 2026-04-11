@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.notifications')" icon-name="notifications">
        <form method="POST"
              action="{{ route('admin.notifications.read-all') }}"
              data-confirm="{{ __('admin.confirm_mark_all_read') }}">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-with-icon">
                <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="m20 6-11 11-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span>{{ __('admin.mark_all_read') }}</span>
            </button>
        </form>
    </x-admin.page-header>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.notifications.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.filter') }}</label>
                    <select name="unread_only" class="form-select">
                        <option value="0" {{ request('unread_only') == '0' ? 'selected' : '' }}>{{ __('admin.all') }}</option>
                        <option value="1" {{ request('unread_only') == '1' ? 'selected' : '' }}>{{ __('admin.unread_only') }}</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{{ __('admin.apply') }}</button>
                </div>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table-card>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('admin.type') }}</th>
                    <th>{{ __('admin.title') }}</th>
                    <th>{{ __('admin.message') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.created_at') }}</th>
                    <th width="130">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $notification)
                    @php
                        $deepLink = $deepLinkResolver($notification);
                    @endphp
                    <tr>
                        <td>{{ $notification->id }}</td>
                        <td><code>{{ $notification->type }}</code></td>
                        <td>{{ $notification->title }}</td>
                        <td>{{ $notification->message }}</td>
                        <td>
                            @if($notification->read_at)
                                <span class="badge status-badge status-read">{{ __('admin.read') }}</span>
                            @else
                                <span class="badge status-badge status-unread">{{ __('admin.unread') }}</span>
                            @endif
                        </td>
                        <td>{{ $notification->created_at }}</td>
                        <td>
                            <div class="table-actions table-actions-stack">
                                @if($deepLink)
                                    <a href="{{ $deepLink }}" class="btn btn-sm btn-action btn-action-open w-100">
                                        {{ __('admin.open') }}
                                    </a>
                                @endif

                                @if(is_null($notification->read_at))
                                    <form method="POST" action="{{ route('admin.notifications.read', $notification) }}" class="w-100">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-action btn-action-mark w-100">
                                            {{ __('admin.mark_read') }}
                                        </button>
                                    </form>
                                @else
                                    <form method="POST"
                                          action="{{ route('admin.notifications.destroy', $notification) }}"
                                          class="w-100"
                                          data-confirm="{{ __('admin.delete_notification_confirmation') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-action btn-action-delete w-100">
                                            {{ __('admin.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.empty-state :message="__('admin.no_notifications_found')" :colspan="7" />
                @endforelse
            </tbody>
        </table>

        {{ $notifications->links() }}
    </x-admin.table-card>
</div>
@endsection

