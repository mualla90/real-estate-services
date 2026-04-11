@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.sliders')" icon-name="sliders">
        @can('sliders.create')
            <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-with-icon">
                <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
                <span>{{ __('admin.add_slider') }}</span>
            </a>
        @endcan
    </x-admin.page-header>

    @if($sliders->count())
        <div class="slider-showcase mb-4">
            @foreach($sliders->take(3) as $slider)
                <div class="slider-preview-card">
                    <div class="slider-preview-media">
                        @if($slider->getFirstMediaUrl('image'))
                            <img src="{{ $slider->getFirstMediaUrl('image') }}" alt="slider-{{ $slider->id }}">
                        @else
                            <div class="slider-preview-placeholder">
                                <span>#{{ $slider->sort_order }}</span>
                            </div>
                        @endif
                        <span class="slider-status-chip {{ $slider->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $slider->is_active ? __('admin.active') : __('admin.inactive') }}
                        </span>
                    </div>
                    <div class="slider-preview-body">
                        <h6 class="mb-1">{{ $slider->getTranslation('title', app()->getLocale()) }}</h6>
                        <p class="mb-2 text-muted small">
                            {{ $slider->subtitle ? $slider->getTranslation('subtitle', app()->getLocale()) : '-' }}
                        </p>
                        <div class="slider-preview-meta">
                            <span>{{ __('admin.sort_order') }}: {{ $slider->sort_order }}</span>
                            <span>{{ __('admin.from') }}: {{ $slider->starts_at?->format('Y-m-d') ?? '-' }}</span>
                            <span>{{ __('admin.to') }}: {{ $slider->ends_at?->format('Y-m-d') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.sliders.index') }}">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">{{ __('admin.search') }}</label>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="{{ __('admin.search_slider') }}"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('admin.all') }}</option>
                        <option value="active" @selected(request('status') === 'active')>{{ __('admin.active') }}</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>{{ __('admin.inactive') }}</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">{{ __('admin.filter') }}</button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary w-100">{{ __('admin.reset') }}</a>
                </div>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table-card>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('admin.slider_image') }}</th>
                    <th>{{ __('admin.title') }}</th>
                    <th>{{ __('admin.slider_link') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.schedule') }}</th>
                    <th>{{ __('admin.sort_order') }}</th>
                    <th width="180">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sliders as $slider)
                    <tr>
                        <td>{{ $slider->id }}</td>
                        <td>
                            @if($slider->getFirstMediaUrl('image'))
                                <img src="{{ $slider->getFirstMediaUrl('image') }}"
                                     alt="slider-{{ $slider->id }}"
                                     class="rounded"
                                     style="width: 90px; height: 54px; object-fit: cover;">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $slider->getTranslation('title', app()->getLocale()) }}</strong>
                            @if($slider->subtitle)
                                <div class="text-muted small">{{ $slider->getTranslation('subtitle', app()->getLocale()) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($slider->link)
                                <a href="{{ $slider->link }}" target="_blank" rel="noopener">{{ __('admin.open') }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($slider->is_active)
                                <span class="badge status-badge status-active">{{ __('admin.active') }}</span>
                            @else
                                <span class="badge status-badge status-inactive">{{ __('admin.inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="small">
                                <div>{{ __('admin.from') }}: {{ $slider->starts_at?->format('Y-m-d H:i') ?? '-' }}</div>
                                <div>{{ __('admin.to') }}: {{ $slider->ends_at?->format('Y-m-d H:i') ?? '-' }}</div>
                            </div>
                        </td>
                        <td>{{ $slider->sort_order }}</td>
                        <td>
                            <div class="table-actions">
                                @can('sliders.update')
                                    <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-sm btn-action btn-action-edit">
                                        {{ __('admin.edit') }}
                                    </a>
                                @endcan

                                @can('sliders.delete')
                                    <form method="POST"
                                          action="{{ route('admin.sliders.destroy', $slider) }}"
                                          class="d-inline"
                                          data-confirm="{{ __('admin.delete_slider_confirmation') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-action btn-action-delete">
                                            {{ __('admin.delete') }}
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.empty-state :message="__('admin.no_sliders_found')" :colspan="8" />
                @endforelse
            </tbody>
        </table>

        {{ $sliders->links() }}
    </x-admin.table-card>
</div>
@endsection
