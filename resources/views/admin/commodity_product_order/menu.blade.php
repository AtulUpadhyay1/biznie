<div class="d-flex flex-wrap gap-2 mb-3 mb-md-0" role="group" aria-label="Basic example">
    <a href="{{ route('admin.commodity-product-order.show', $data->id) }}"
        class="btn btn-sm {{ $is_active == 'show' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
        wire:navigate>
        <i class="bi bi-eye icon-sm"></i> View
    </a>
    <a href="{{ route('admin.commodity-product-order.ledger', $data->id) }}"
        class="btn btn-sm {{ $is_active == 'ledger' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
        wire:navigate>
        <i class="bi bi-file-earmark-ruled icon-sm"></i> Buyer Ledger
    </a>
    <a href="{{ route('admin.commodity-product-order.sellerLedger', $data->id) }}"
        class="btn btn-sm {{ $is_active == 'sellerLedger' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
        wire:navigate>
        <i class="bi bi-file-ruled icon-sm"></i> Seller Ledger
    </a>
    <a href="{{ route('admin.commodity-product-order.transporterLedger', $data->id) }}"
        class="btn btn-sm {{ $is_active == 'transporterLedger' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
        wire:navigate>
        <i class="bi bi-truck icon-sm"></i> Transporter Ledger
    </a>
    <a href="{{ route('admin.commodity-product-order.receive-payment', $data->id) }}"
        class="btn btn-sm {{ $is_active == 'receive-payment' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
        wire:navigate>
        <i class="bi bi-credit-card-2-front icon-sm"></i> Payment from Buyer
    </a>
    <a href="{{ route('admin.commodity-product-order.send-payment', $data->id) }}"
        class="btn btn-sm {{ $is_active == 'send-payment' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
        wire:navigate>
        <i class="bi bi-credit-card icon-sm"></i> Payment to Seller
    </a>
    <a href="{{ route('admin.commodity-product-order.status', $data->id) }}"
        class="btn btn-sm {{ $is_active == 'status' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
        wire:navigate>
        <i class="bi bi-device-ssd icon-sm"></i> Update Status
    </a>
    <a href="{{ route('admin.commodity-product-order.history', $data->id) }}"
        class="btn btn-sm {{ $is_active == 'history' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
        wire:navigate>
        <i class="bi bi-clock-history icon-sm"></i> History
    </a>
    <a href="{{ route('admin.commodity-product-order.transporter', $data->id) }}"
        class="btn btn-sm {{ $is_active == 'transporter' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
        wire:navigate>
        <i class="bi bi-truck icon-sm"></i> Transporter
    </a>
    @if ($data->status != 'cancel' && $data->status != 'delivered')
        <a href="{{ route('admin.commodity-product-order.edit', $data->id) }}"
            class="btn btn-sm {{ $is_active == 'edit' ? 'btn-danger' : 'btn-outline-danger' }} btn-icon-text flex-grow-1 flex-md-grow-0"
            wire:navigate>
            <i class="bi bi-pencil-square icon-sm"></i> Edit
        </a>
    @endif
</div>
