@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">{{ __('admin.admins') }}</h3>

        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
            {{ __('admin.add_admin') }}
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
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
        </div>
    </div>

    <div class="card">
        <div class="card-body">
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
                                    <span class="badge bg-danger ms-2">{{ __('admin.protected') }}</span>
                                @endif
                            </td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->roles->first()?->name ?? '-' }}</td>
                            <td>
                                @if($admin->is_active)
                                    <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                @if(auth('admin')->user()?->hasRole('super_admin'))
                                    <a href="{{ route('admin.admins.edit', $admin) }}"
                                       class="btn btn-sm btn-warning {{ $admin->hasRole('super_admin') ? 'disabled' : '' }}">
                                        {{ __('admin.edit_admin') }}
                                    </a>

                                    @if(! $admin->hasRole('super_admin'))
                                        <form method="POST"
                                              action="{{ route('admin.admins.destroy', $admin) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('{{ __('admin.delete_admin_confirmation') }}')">
                                                {{ __('admin.delete_admin') }}
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('admin.no_admins_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $admins->links() }}
        </div>
    </div>

</div>
@endsection
