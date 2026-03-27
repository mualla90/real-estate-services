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
                    <span class="badge bg-primary">
                        {{ auth('admin')->user()?->name }}
                    </span>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            Logout
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
                            <div class="card-header">Menu</div>

                            <div class="list-group list-group-flush">

                                <a href="{{ route('admin.dashboard') }}"
                                class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                Dashboard
                                </a>

                                <a href="{{ route('admin.admins.index') }}"
                                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                                        Admins
                                </a>
                                <a href="{{ route('admin.business-accounts.index') }}"
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.business-accounts.*') ? 'active' : '' }}">
                                    Business Accounts
                                </a>

                                <a href="{{ route('admin.cities.index') }}"
                                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                                        Cities
                                </a>

                                <a href="{{ route('admin.categories.index') }}"
                                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                        Categories
                                </a>

                                <a href="{{ route('admin.subcategories.index') }}"
                                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                                        Subcategories
                                </a>

                                <a href="{{ route('admin.activity-types.index') }}"
                                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.activity-types.*') ? 'active' : '' }}">
                                        Activity Types
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
