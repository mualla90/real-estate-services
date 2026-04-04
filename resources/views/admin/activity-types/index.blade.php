@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">{{ __('admin.activity_types') }}</h3>

        <a href="{{ route('admin.activity-types.create') }}" class="btn btn-primary">
            {{ __('admin.add_activity_type') }}
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-types.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.search') }}</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="{{ __('admin.search_activity_type') }}"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('admin.filter') }}
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.activity-types.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th>{{ __('admin.english_name') }}</th>
                        <th>{{ __('admin.arabic_name') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th>{{ __('admin.sort_order') }}</th>
                        <th width="180">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($activityTypes as $activityType)
                        <tr>
                            <td>{{ $activityType->id }}</td>
                            <td>{{ $activityType->getTranslation('name', 'en') }}</td>
                            <td>{{ $activityType->getTranslation('name', 'ar') }}</td>
                            <td>
                                @if($activityType->is_active)
                                    <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>{{ $activityType->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.activity-types.edit', $activityType) }}" class="btn btn-sm btn-warning">
                                    {{ __('admin.edit_activity_type') }}
                                </a>

                                <form method="POST" action="{{ route('admin.activity-types.destroy', $activityType) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('{{ __('admin.delete_confirmation') }}')">
                                        {{ __('admin.delete_activity_type') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('admin.no_activity_types_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $activityTypes->links() }}
        </div>
    </div>

</div>
@endsection
