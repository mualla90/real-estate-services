<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="admin-body"
      data-ui-confirm-title="{{ __('admin.confirm_action') }}"
      data-ui-confirm-approve="{{ __('admin.confirm') }}"
      data-ui-cancel="{{ __('admin.cancel') }}"
      @auth('admin')
          data-admin-id="{{ auth('admin')->id() }}"
          data-admin-realtime-auth-endpoint="{{ route('admin.realtime.pusher.auth') }}"
          data-pusher-key="{{ config('broadcasting.connections.pusher.key') }}"
          data-pusher-cluster="{{ config('broadcasting.connections.pusher.options.cluster') }}"
          data-pusher-scheme="{{ config('broadcasting.connections.pusher.options.scheme', 'https') }}"
      @endauth>
    <div class="wrapper d-flex flex-column min-vh-100 admin-shell">
        <header class="header header-sticky admin-topbar">
            <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="header-brand text-decoration-none admin-brand">
                    {{ config('app.name') }}
                </a>

                <div class="d-flex flex-wrap align-items-center gap-2 admin-top-actions">
                    @php
                        $adminUser = auth('admin')->user();
                        $adminUnreadNotificationsCount = $adminUser
                            ? $adminUser->appNotifications()->whereNull('read_at')->count()
                            : 0;
                        $adminLatestNotifications = $adminUser
                            ? $adminUser->appNotifications()->latest('id')->limit(5)->get()
                            : collect();
                    @endphp

                    @can('notifications.view')
                        <div class="dropdown admin-notification-menu">
                            <button class="btn btn-sm btn-outline-secondary position-relative btn-with-icon"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside"
                                    aria-expanded="false">
                                <span class="btn-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0h6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                {{ __('admin.notifications') }}
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $adminUnreadNotificationsCount > 0 ? '' : 'd-none' }}"
                                      data-live-stat="unread_notifications">
                                    {{ $adminUnreadNotificationsCount }}
                                </span>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end admin-notification-dropdown">
                                <div class="admin-notification-dropdown-header">
                                    <span>{{ __('admin.notifications') }}</span>
                                    <a href="{{ route('admin.notifications.index') }}">{{ __('admin.open') }}</a>
                                </div>

                                <div class="admin-notification-dropdown-list">
                                    @forelse($adminLatestNotifications as $notification)
                                        <a href="{{ route('admin.notifications.open', $notification) }}"
                                           class="admin-notification-preview {{ is_null($notification->read_at) ? 'is-unread' : '' }}">
                                            <span class="admin-notification-dot" aria-hidden="true"></span>
                                            <span class="admin-notification-preview-body">
                                                <span class="admin-notification-preview-title">{{ $notification->title }}</span>
                                                <span class="admin-notification-preview-text">{{ $notification->message }}</span>
                                                <span class="admin-notification-preview-time">{{ optional($notification->created_at)->diffForHumans() }}</span>
                                            </span>
                                        </a>
                                    @empty
                                        <div class="admin-notification-empty" data-admin-notification-empty>
                                            {{ __('admin.no_notifications_found') }}
                                        </div>
                                    @endforelse
                                </div>

                                @can('notifications.manage')
                                    @if($adminUnreadNotificationsCount > 0)
                                        <form method="POST" action="{{ route('admin.notifications.read-all') }}" class="admin-notification-dropdown-footer">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                                {{ __('admin.mark_all_read') }}
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    @endcan

                    <div class="d-flex align-items-center gap-2 lang-switcher">
                        <a href="{{ route('lang.switch', 'en') }}"
                           class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-outline-primary' }}">
                            EN
                        </a>

                        <a href="{{ route('lang.switch', 'ar') }}"
                           class="btn btn-sm {{ app()->getLocale() === 'ar' ? 'btn-primary' : 'btn-outline-primary' }}">
                            AR
                        </a>
                    </div>

                    <span class="badge bg-primary admin-user-badge">
                        {{ auth('admin')->user()?->name }}
                    </span>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            {{ __('admin.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="body flex-grow-1">
            <div class="container-fluid">
                <div class="row g-4">
                    <aside class="col-lg-3 col-xl-2 mb-4">
                        <div class="card admin-sidebar-card">
                            <div class="card-header">{{ __('admin.menu') }}</div>

                            <div class="list-group list-group-flush admin-menu-list">
                                <a href="{{ route('admin.dashboard') }}"
                                   class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <span class="admin-menu-link-content">
                                        <span class="admin-menu-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none"><path d="M3 13h8V3H3v10Zm10 8h8V11h-8v10ZM3 21h8v-6H3v6Zm10-10h8V3h-8v8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                        </span>
                                        <span>{{ __('admin.dashboard') }}</span>
                                    </span>
                                </a>

                                @can('admins.view')
                                    <a href="{{ route('admin.admins.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10.5 10v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.admins') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('roles.view')
                                    <a href="{{ route('admin.roles.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="m12 15 5.2 3-1.5-5.9L20 8.4l-6-.5L12 2 10 7.9l-6 .5 4.3 3.7L6.8 18 12 15Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.roles') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('business-accounts.view')
                                    <a href="{{ route('admin.business-accounts.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.business-accounts.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M3 7h18M5 7V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2M6 11h12v8H6z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.business_accounts') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('services.view')
                                    <a href="{{ route('admin.services.review.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4zM8 9h8M8 13h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.services') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('services.view')
                                    <a href="{{ route('admin.chat-demo.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.chat-demo.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 5h16v10H8l-4 4V5Zm5 4h6M9 12h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span>Chat Demo</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('cities.view')
                                    <a href="{{ route('admin.cities.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 20h16M6 20V8l6-3v15M18 20V11l-6-3" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.cities') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('categories.view')
                                    <a href="{{ route('admin.categories.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 6h7v7H4zM13 6h7v7h-7zM4 15h7v3H4zM13 15h7v3h-7z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.categories') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('subcategories.view')
                                    <a href="{{ route('admin.subcategories.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M8 12h12M12 17h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.subcategories') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('activity-types.view')
                                    <a href="{{ route('admin.activity-types.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.activity-types.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="m3 12 4-4 4 4 4-4 6 6M3 19h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.activity_types') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('sliders.view')
                                    <a href="{{ route('admin.sliders.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 17h16M7 7v10m10-10v10M10 12h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.sliders') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('dynamic-fields.view')
                                    <a href="{{ route('admin.dynamic-fields.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.dynamic-fields.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h10M4 18h16M14 12h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.dynamic_fields') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('reports.view')
                                    <a href="{{ route('admin.reports.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M6 4h9l3 3v13H6zM9 13h6M9 17h6M9 9h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.reports') }}</span>
                                        </span>
                                    </a>
                                @endcan

                                @can('notifications.view')
                                    <a href="{{ route('admin.notifications.index') }}"
                                       class="list-group-item list-group-item-action admin-menu-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                                        <span class="admin-menu-link-content">
                                            <span class="admin-menu-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0h6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span>{{ __('admin.notifications') }}</span>
                                        </span>
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </aside>

                    <main class="col-lg-9 col-xl-10 admin-content">
                        <div class="flash-stack">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-toast-container" id="adminToastContainer" aria-live="polite" aria-atomic="true"></div>

    <div class="admin-confirm-backdrop" id="adminConfirmBackdrop" hidden>
        <div class="admin-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="adminConfirmTitle">
            <h5 class="admin-confirm-title" id="adminConfirmTitle">{{ __('admin.confirm_action') }}</h5>
            <p class="admin-confirm-message mb-0" id="adminConfirmMessage"></p>
            <div class="admin-confirm-actions">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="adminConfirmCancel">
                    {{ __('admin.cancel') }}
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="adminConfirmApprove">
                    {{ __('admin.confirm') }}
                </button>
            </div>
        </div>
    </div>
    @include('partials.firebase')
</body>
</html>
