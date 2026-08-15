<div class="bz-tabs mb-3 mb-md-0" role="group" aria-label="Basic example">
    <a href="{{ route('admin.commodity-product-order.show', $data->id) }}"
        class="bz-tab {{ $is_active == 'show' ? 'is-active' : '' }}"
        wire:navigate>
        <i class="bi bi-eye"></i> View
    </a>
    <a href="{{ route('admin.commodity-product-order.ledger', $data->id) }}"
        class="bz-tab {{ $is_active == 'ledger' ? 'is-active' : '' }}"
        wire:navigate>
        <i class="bi bi-file-earmark-ruled"></i> Buyer Ledger
    </a>
    <a href="{{ route('admin.commodity-product-order.sellerLedger', $data->id) }}"
        class="bz-tab {{ $is_active == 'sellerLedger' ? 'is-active' : '' }}"
        wire:navigate>
        <i class="bi bi-file-ruled"></i> Seller Ledger
    </a>
    <a href="{{ route('admin.commodity-product-order.transporterLedger', $data->id) }}"
        class="bz-tab {{ $is_active == 'transporterLedger' ? 'is-active' : '' }}"
        wire:navigate>
        <i class="bi bi-truck"></i> Transporter Ledger
    </a>
    <a href="{{ route('admin.commodity-product-order.receive-payment', $data->id) }}"
        class="bz-tab {{ $is_active == 'receive-payment' ? 'is-active' : '' }}"
        wire:navigate>
        <i class="bi bi-credit-card-2-front"></i> Payment from Buyer
    </a>
    <a href="{{ route('admin.commodity-product-order.send-payment', $data->id) }}"
        class="bz-tab {{ $is_active == 'send-payment' ? 'is-active' : '' }}"
        wire:navigate>
        <i class="bi bi-credit-card"></i> Payment to Seller
    </a>
    <a href="{{ route('admin.commodity-product-order.status', $data->id) }}"
        class="bz-tab {{ $is_active == 'status' ? 'is-active' : '' }}"
        wire:navigate>
        <i class="bi bi-device-ssd"></i> Update Status
    </a>
    <a href="{{ route('admin.commodity-product-order.history', $data->id) }}"
        class="bz-tab {{ $is_active == 'history' ? 'is-active' : '' }}"
        wire:navigate>
        <i class="bi bi-clock-history"></i> History
    </a>
    <a href="{{ route('admin.commodity-product-order.transporter', $data->id) }}"
        class="bz-tab {{ $is_active == 'transporter' ? 'is-active' : '' }}"
        wire:navigate>
        <i class="bi bi-truck"></i> Transporter
    </a>
    @if ($data->status != 'cancel' && $data->status != 'delivered')
        <a href="{{ route('admin.commodity-product-order.edit', $data->id) }}"
            class="bz-tab {{ $is_active == 'edit' ? 'is-active' : '' }}"
            wire:navigate>
            <i class="bi bi-pencil-square"></i> Edit
        </a>
    @endif
</div>
