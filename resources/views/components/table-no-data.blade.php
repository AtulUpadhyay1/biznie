@props([
    'title' => 'Nothing here yet',
    'text' => 'No records match the current filters.',
    'icon' => 'bi-inbox',
    'colspan' => 10,
])

<tr class="bz-empty-row">
    <td colspan="{{ $colspan }}">
        <div class="bz-empty">
            <span class="bz-empty__icon"><i class="bi {{ $icon }}"></i></span>
            <span class="bz-empty__title">{{ $title }}</span>
            <p class="bz-empty__text">{{ $text }}</p>
        </div>
    </td>
</tr>
