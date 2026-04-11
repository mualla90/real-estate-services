@props([
    'title',
    'subtitle' => null,
    'iconName' => null,
])

<div class="admin-page-header mb-4">
    @php
        $icons = [
            'dashboard' => '<svg viewBox="0 0 24 24" fill="none"><path d="M3 13h8V3H3v10Zm10 8h8V11h-8v10ZM3 21h8v-6H3v6Zm10-10h8V3h-8v8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'admins' => '<svg viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10.5 10v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'roles' => '<svg viewBox="0 0 24 24" fill="none"><path d="m12 15 5.2 3-1.5-5.9L20 8.4l-6-.5L12 2 10 7.9l-6 .5 4.3 3.7L6.8 18 12 15Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'business' => '<svg viewBox="0 0 24 24" fill="none"><path d="M3 7h18M5 7V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2M6 11h12v8H6z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'services' => '<svg viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4zM8 9h8M8 13h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'categories' => '<svg viewBox="0 0 24 24" fill="none"><path d="M4 6h7v7H4zM13 6h7v7h-7zM4 15h7v3H4zM13 15h7v3h-7z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'sliders' => '<svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 17h16M7 7v10m10-10v10M10 12h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'cities' => '<svg viewBox="0 0 24 24" fill="none"><path d="M4 20h16M6 20V8l6-3v15M18 20V11l-6-3" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'dynamic' => '<svg viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h10M4 18h16M14 12h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'reports' => '<svg viewBox="0 0 24 24" fill="none"><path d="M6 4h9l3 3v13H6zM9 13h6M9 17h6M9 9h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'notifications' => '<svg viewBox="0 0 24 24" fill="none"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0h6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ];
    @endphp

    <div class="page-title-wrap">
        <h3 class="mb-0 page-title d-flex align-items-center gap-2">
            @if($iconName && isset($icons[$iconName]))
                <span class="page-title-icon" aria-hidden="true">{!! $icons[$iconName] !!}</span>
            @endif
            <span>{{ $title }}</span>
        </h3>
        @if($subtitle)
            <p class="page-subtitle mb-0 mt-2">{{ $subtitle }}</p>
        @endif
    </div>

    @if(trim((string) $slot))
        <div class="page-actions d-flex flex-wrap gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
