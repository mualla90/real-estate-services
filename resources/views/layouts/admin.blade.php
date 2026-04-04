<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="wrapper d-flex flex-column min-vh-100 bg-light">
        <header class="header header-sticky mb-4 px-3">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.dashboard') }}" class="header-brand text-decoration-none">
                    {{ config('app.name') }}
                </a>

                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('lang.switch', 'en') }}"
                           class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-outline-primary' }}">
                            EN
                        </a>

                        <a href="{{ route('lang.switch', 'ar') }}"
                           class="btn btn-sm {{ app()->getLocale() === 'ar' ? 'btn-primary' : 'btn-outline-primary' }}">
                            AR
                        </a>
                    </div>

                    <span class="badge bg-primary">
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
                <div class="row">
                    <aside class="col-md-3 col-lg-2 mb-4">
                        <div class="card">
                            <div class="card-header">{{ __('admin.menu') }}</div>

                            <div class="list-group list-group-flush">
                                <a href="{{ route('admin.dashboard') }}"
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    {{ __('admin.dashboard') }}
                                </a>

                                @can('admins.view')
                                    <a href="{{ route('admin.admins.index') }}"
                                       class="list-group-item list-group-item-action {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                                        {{ __('admin.admins') }}
                                    </a>
                                @endcan

                                @can('roles.view')
                                    <a href="{{ route('admin.roles.index') }}"
                                       class="list-group-item list-group-item-action {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                        {{ __('admin.roles') }}
                                    </a>
                                @endcan

                                <a href="{{ route('admin.business-accounts.index') }}"
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.business-accounts.*') ? 'active' : '' }}">
                                    {{ __('admin.business_accounts') }}
                                </a>

                                <a href="{{ route('admin.services.review.index') }}"
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                                    {{ __('admin.services') }}
                                </a>

                                <a href="{{ route('admin.cities.index') }}"
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                                    {{ __('admin.cities') }}
                                </a>

                                <a href="{{ route('admin.categories.index') }}"
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                    {{ __('admin.categories') }}
                                </a>

                                <a href="{{ route('admin.subcategories.index') }}"
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                                    {{ __('admin.subcategories') }}
                                </a>

                                <a href="{{ route('admin.activity-types.index') }}"
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.activity-types.*') ? 'active' : '' }}">
                                    {{ __('admin.activity_types') }}
                                </a>
                            </div>
                        </div>
                    </aside>

                    <main class="col-md-9 col-lg-10">
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

                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
