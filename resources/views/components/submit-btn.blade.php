@php
    // Without a $function this is the form's submit button; with one it is a
    // standalone action (e.g. inside a modal that lives outside the <form>).
    $target = $function ?: null;
@endphp
<button class="btn btn-danger btn-sm" type="{{ $function ? 'button' : 'submit' }}" title="{{ $text }}"
    @if ($function) wire:click="{{ $function }}" @endif
    wire:loading.attr="disabled" @if ($target) wire:target="{{ $target }}" @endif>
    <i class="bi bi-check-lg" wire:loading.remove @if ($target) wire:target="{{ $target }}" @endif></i>
    <span class="bz-spinner bz-spinner--sm" wire:loading @if ($target) wire:target="{{ $target }}" @endif></span>
    {{ $text }}
</button>
