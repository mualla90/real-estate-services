@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">

    <x-admin.page-header :title="__('admin.admins')" icon-name="admins">
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary btn-with-icon">
            <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
            <span>{{ __('admin.add_admin') }}</span>
        </a>
    </x-admin.page-header>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.admins.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.search') }}</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="{{ __('admin.search_admin') }}"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        {{ __('admin.filter') }}
                    </button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary w-100">
                        {{ __('admin.reset') }}
                    </a>
                </div>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table-card>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('admin.name') }}</th>
                    <th>{{ __('admin.email') }}</th>
                    <th>{{ __('admin.role') }}</th>
                    <th>{{ __('admin.active') }}</th>
                    <th width="120">{{ __('admin.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td>
                            {{ $admin->name }}

                            @if($admin->hasRole('super_admin'))
                                <span class="badge status-badge status-rejected ms-2">{{ __('admin.protected') }}</span>
                            @endif
                        </td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->roles->first()?->name ?? '-' }}</td>
                        <td>
                            @if($admin->is_active)
                                <span class="badge status-badge status-active">{{ __('admin.active') }}</span>
                            @else
                                <span class="badge status-badge status-inactive">{{ __('admin.inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                @if(auth('admin')->user()?->hasRole('super_admin'))
                                    <a href="{{ route('admin.admins.edit', $admin) }}"
                                       class="btn btn-sm btn-action btn-action-edit {{ $admin->hasRole('super_admin') ? 'disabled' : '' }}">
                                        {{ __('admin.edit_admin') }}
                                    </a>

                                    @if(! $admin->hasRole('super_admin'))
                                        <form method="POST"
                                              action="{{ route('admin.admins.destroy', $admin) }}"
                                              class="d-inline"
                                              data-confirm="{{ __('admin.delete_admin_confirmation') }}">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-action btn-action-delete">
                                                {{ __('admin.delete_admin') }}
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.empty-state :message="__('admin.no_admins_found')" :colspan="6" />
                @endforelse
            </tbody>
        </table>

        {{ $admins->links() }}
    </x-admin.table-card>

</div>
@endsection
