@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">{{ __('admin.roles') }}</h3>

        @if(auth('admin')->user()?->can('roles.create'))
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                {{ __('admin.add_role') }}
            </a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
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
                                    <span class="badge bg-danger ms-2">
                                        {{ __('admin.protected') }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $role->permissions_count }}</td>
                            <td>{{ $role->created_at?->format('Y-m-d') }}</td>
                            <td>
                                @if(auth('admin')->user()?->can('roles.update'))
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="btn btn-sm btn-warning {{ $role->name === 'super_admin' ? 'disabled' : '' }}">
                                        {{ __('admin.editRole') }}
                                    </a>
                                @endif

                                @if(auth('admin')->user()?->can('roles.delete') && $role->name !== 'super_admin')
                                    <form method="POST"
                                          action="{{ route('admin.roles.destroy', $role) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('{{ __('admin.delete_role_confirmation') }}')">
                                            {{ __('admin.deleteRole') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                {{ __('admin.no_roles_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $roles->links() }}
        </div>
    </div>

</div>
@endsection
