@props([
    'message',
    'colspan' => 1,
    'icon' => '◌',
    'actionLabel' => null,
    'actionUrl' => null,
])

<tr>
    <td colspan="{{ $colspan }}" class="text-center table-empty-state">
        <div class="empty-state">
            <div class="empty-state-icon">{{ $icon }}</div>
            <p class="empty-state-text mb-0">{{ $message }}</p>

            @if($actionLabel && $actionUrl)
                <a href="{{ $actionUrl }}" class="btn btn-sm btn-primary mt-2">{{ $actionLabel }}</a>
            @endif
        </div>
    </td>
</tr>
