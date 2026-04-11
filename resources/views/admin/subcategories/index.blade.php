@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">

    <div class="d-flex justify-content-between align-items-center mb-4 admin-page-header">
        <h3 class="mb-0 page-title">{{ __('admin.subcategories') }}</h3>

        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
            {{ __('admin.add_subcategory') }}
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subcategories.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.search') }}</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="{{ __('admin.search_subcategory') }}"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('admin.category') }}</label>
                        <select name="category_id" class="form-select">
                            <option value="">{{ __('admin.all') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                    {{ $category->getTranslation('name', app()->getLocale()) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('admin.filter') }}
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary w-100">
                            {{ __('admin.reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive"><table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin.english_name') }}</th>
                        <th>{{ __('admin.arabic_name') }}</th>
                        <th>{{ __('admin.category') }}</th>
                        <th>{{ __('admin.active') }}</th>
                        <th>{{ __('admin.sort_order') }}</th>
                        <th width="180">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($subcategories as $subcategory)
                        <tr>
                            <td>{{ $subcategory->id }}</td>
                            <td>{{ $subcategory->getTranslation('name', 'en') }}</td>
                            <td>{{ $subcategory->getTranslation('name', 'ar') }}</td>
                            <td>{{ $subcategory->category?->getTranslation('name', app()->getLocale()) }}</td>
                            <td>
                                @if($subcategory->is_active)
                                    <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>{{ $subcategory->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-sm btn-action btn-action-edit">
                                    {{ __('admin.edit_subcategory') }}
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.subcategories.destroy', $subcategory) }}"
                                      class="d-inline"
                                      data-confirm="{{ __('admin.delete_confirmation') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-action btn-action-delete">
                                        {{ __('admin.delete_subcategory') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('admin.no_subcategories_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table></div>

            {{ $subcategories->links() }}
        </div>
    </div>

</div>
@endsection



