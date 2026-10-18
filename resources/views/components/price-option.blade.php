{{-- The Price Option the buyer picked when raising the RFQ (App\Enums\PriceOption). --}}
@props(['value' => null])

@php($option = \App\Enums\PriceOption::tryFrom((string) $value))

@if ($option)
    <span class="bz-status bz-status--info">{{ $option->label() }}</span>
    <small class="text-muted d-block">{{ $option->hint() }}</small>
@else
    <span class="text-muted">Not chosen</span>
@endif
