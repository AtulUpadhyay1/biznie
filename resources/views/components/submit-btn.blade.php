<button class="btn btn-danger btn-sm" type="submit" title="{{ $text }}" wire:loading.attr="disabled">
    <i class="bi bi-check-lg" wire:loading.remove></i>
    <span class="bz-spinner bz-spinner--sm" wire:loading></span>
    {{ $text }}
</button>
