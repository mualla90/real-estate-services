@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">

    <x-admin.page-header :title="__('admin.categories')" icon-name="categories">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-with-icon">
            <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
            <span>{{ __('admin.add_category') }}</span>
        </a>
    </x-admin.page-header>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.categories.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.search') }}</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="{{ __('admin.search_category') }}"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        {{ __('admin.filter') }}
                    </button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100">
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
                    <th>{{ __('admin.english_name') }}</th>
                    <th>{{ __('admin.arabic_name') }}</th>
                    <th>{{ __('admin.description') }}</th>
                    <th>{{ __('admin.active') }}</th>
                    <th>{{ __('admin.sort_order') }}</th>
                    <th width="180">{{ __('admin.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->getTranslation('name', 'en') }}</td>
                        <td>{{ $category->getTranslation('name', 'ar') }}</td>
                        <td>{{ $category->getTranslation('description', app()->getLocale()) }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge status-badge status-active">{{ __('admin.active') }}</span>
                            @else
                                <span class="badge status-badge status-inactive">{{ __('admin.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $category->sort_order }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-action btn-action-edit">
                                    {{ __('admin.edit_category') }}
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.categories.destroy', $category) }}"
                                      class="d-inline"
                                      data-confirm="{{ __('admin.delete_confirmation') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-action btn-action-delete">
                                        {{ __('admin.delete_category') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.empty-state :message="__('admin.no_categories_found')" :colspan="7" />
                @endforelse
            </tbody>
        </table>

        {{ $categories->links() }}
    </x-admin.table-card>

</div>
@endsection
