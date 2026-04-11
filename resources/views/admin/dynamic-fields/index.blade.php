@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.dynamic_fields')" icon-name="dynamic">
        <a href="{{ route('admin.dynamic-fields.create') }}" class="btn btn-primary btn-with-icon">
            <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
            <span>{{ __('admin.add_dynamic_field') }}</span>
        </a>
    </x-admin.page-header>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.dynamic-fields.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.search') }}</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="{{ __('admin.search_dynamic_field') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('admin.all') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">{{ __('admin.filter') }}</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.dynamic-fields.index') }}" class="btn btn-outline-secondary w-100">{{ __('admin.reset') }}</a>
                </div>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table-card>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('admin.field_key') }}</th>
                    <th>{{ __('admin.type') }}</th>
                    <th>{{ __('admin.english_name') }}</th>
                    <th>{{ __('admin.category') }}</th>
                    <th>{{ __('admin.subcategory') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.required') }}</th>
                    <th>{{ __('admin.sort_order') }}</th>
                    <th width="180">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dynamicFields as $field)
                    <tr>
                        <td>{{ $field->id }}</td>
                        <td><code>{{ $field->field_key }}</code></td>
                        <td>{{ __('admin.field_type_' . $field->field_type) }}</td>
                        <td>{{ $field->getTranslation('name', 'en') }}</td>
                        <td>{{ $field->category?->getTranslation('name', app()->getLocale()) }}</td>
                        <td>{{ $field->subcategory?->getTranslation('name', app()->getLocale()) }}</td>
                        <td>
                            <span class="badge status-badge status-{{ $field->status }}">
                                {{ __('admin.' . $field->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge status-badge {{ $field->is_required ? 'status-required' : 'status-optional' }}">
                                {{ $field->is_required ? __('admin.yes') : __('admin.no') }}
                            </span>
                        </td>
                        <td>{{ $field->sort_order }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.dynamic-fields.edit', $field) }}" class="btn btn-sm btn-action btn-action-edit">{{ __('admin.edit') }}</a>
                                <form method="POST"
                                      action="{{ route('admin.dynamic-fields.destroy', $field) }}"
                                      class="d-inline"
                                      data-confirm="{{ __('admin.delete_dynamic_field_confirmation') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-action btn-action-delete">
                                        {{ __('admin.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.empty-state :message="__('admin.no_dynamic_fields_found')" :colspan="10" />
                @endforelse
            </tbody>
        </table>

        {{ $dynamicFields->links() }}
    </x-admin.table-card>
</div>
@endsection

