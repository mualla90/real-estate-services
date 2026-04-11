@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">

    <x-admin.page-header :title="__('admin.roles')" icon-name="roles">
        @if(auth('admin')->user()?->can('roles.create'))
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-with-icon">
                <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
                <span>{{ __('admin.add_role') }}</span>
            </a>
        @endif
    </x-admin.page-header>

    <x-admin.table-card>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('admin.name') }}</th>
                    <th>{{ __('admin.permissions_count') }}</th>
                    <th>{{ __('admin.created_at') }}</th>
                    <th width="180">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>
                            {{ $role->name }}

                            @if($role->name === 'super_admin')
                                <span class="badge status-badge status-rejected ms-2">
                                    {{ __('admin.protected') }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $role->permissions_count }}</td>
                        <td>{{ $role->created_at?->format('Y-m-d') }}</td>
                        <td>
                            <div class="table-actions">
                                @if(auth('admin')->user()?->can('roles.update'))
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="btn btn-sm btn-action btn-action-edit {{ $role->name === 'super_admin' ? 'disabled' : '' }}">
                                        {{ __('admin.editRole') }}
                                    </a>
                                @endif

                                @if(auth('admin')->user()?->can('roles.delete') && $role->name !== 'super_admin')
                                    <form method="POST"
                                          action="{{ route('admin.roles.destroy', $role) }}"
                                          class="d-inline"
                                          data-confirm="{{ __('admin.delete_role_confirmation') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-action btn-action-delete">
                                            {{ __('admin.deleteRole') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.empty-state :message="__('admin.no_roles_found')" :colspan="5" />
                @endforelse
            </tbody>
        </table>

        {{ $roles->links() }}
    </x-admin.table-card>

</div>
@endsection
