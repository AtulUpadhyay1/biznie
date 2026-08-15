<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a type="button" class="btn btn-danger btn-sm" title="add"
                            href="{{ route('admin.seller-product.add', $user_id) }}" wire:navigate>
                            <i class="bi bi-plus-lg"></i>
                            Add
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Base Price</th>
                                    <th>Price Validity</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->getBrand ? $data->getBrand->name : '--' }} </td>
                                        <td>{{ isset($data->getStatePrice[0]) ? $data->getStatePrice[0]->state : '--' }}
                                        </td>
                                        <td>{{ isset($data->getStatePrice[0]) ? $data->getStatePrice[0]->city : '--' }}
                                        </td>
                                        <td>
                                            {{ formatIndianNumber($data->base_price) }}
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-icon" data-bs-toggle="modal" data-bs-target="#exampleModal" title="View Calculation" wire:click="viewPriceCalculation({{ $data->id }})">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        </td>
                                        <td>
                                            @if ($data->price_validity)
                                                {{ $data->price_validity }}
                                                <br><small class="text-danger">
                                                    @if (\Carbon\Carbon::parse($data->price_validity)->isPast())
                                                        Expired
                                                    @else
                                                        Expire In
                                                    @endif
                                                    {{ \Carbon\Carbon::parse($data->price_validity)->diffForHumans() }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $data->quantity }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input"
                                                    id="product_status_{{ $data->id }}"
                                                    {{ $data->status == 'active' ? 'checked' : '' }}
                                                    wire:change="updateStatus({{ $data->id }})">
                                                <label class="form-check-label visually-hidden"
                                                    for="product_status_{{ $data->id }}">Status</label>
                                            </div>
                                        </td>
                                        <td>
                                            <a type="button" class="bz-row-action" id="ActionBtn_{{ $data->id }}"
                                                data-bs-toggle="dropdown" role="button" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn_{{ $data->id }}">
                                                <a href="{{ route('admin.seller-product.show', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-eye me-2"></i><span>View</span></a>
                                                <a href="{{ route('admin.seller-product.edit', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-pencil-square me-2"></i><span>Edit</span></a>
                                                <a href="{{ route('admin.seller-product.variation', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-card-checklist me-2"></i><span>Variant</span></a>
                                                <a href="{{ route('admin.seller-product.quality', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-cart-check me-2"></i><span>Quality</span></a>
                                                <a href="{{ route('admin.seller-product.price', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-currency-rupee me-2"></i><span>Update
                                                        Price</span></a>
                                                <a href="{{ route('admin.seller-product.stock', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-database me-2"></i><span>Update
                                                        Stock</span></a>
                                                <a href="{{ route('admin.seller-product.for-price', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-geo-alt me-2"></i><span>F.O.R
                                                        Price</span></a>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View Calculation</h5>
                </div>
                <div class="modal-body">
                    @if($detail)
                        <dl class="bz-kv-list">
                            <div>
                                <dt>Base Price</dt>
                                <dd>Rs {{ formatIndianNumber($detail['base_price']) }}</dd>
                            </div>
                            <div>
                                <dt>Guage Difference</dt>
                                <dd>+ Rs {{ formatIndianNumber($detail['default_variation_price']) }}</dd>
                            </div>
                            @if($detail['loading_charge'] > 0)
                                <div>
                                    <dt>Loading Charge</dt>
                                    <dd>+ Rs {{ formatIndianNumber($detail['loading_charge']) }}</dd>
                                </div>
                            @endif
                            @if($detail['insurance_charge'] > 0)
                                <div>
                                    <dt>Insurance Charge</dt>
                                    <dd>+ Rs {{ formatIndianNumber($detail['insurance_charge']) }}</dd>
                                </div>
                            @endif
                            @if ($detail['quality_charge'] > 0)
                                <div>
                                    <dt>Quality Charge</dt>
                                    <dd>+ Rs {{ formatIndianNumber($detail['quality_charge']) }}</dd>
                                </div>
                            @endif
                            @foreach ($detail['other_charges'] as $charge)
                                <div>
                                    <dt>{{ $charge['name'] }}</dt>
                                    <dd>{{ $charge['operator'] }} Rs {{ formatIndianNumber($charge['price']) }}</dd>
                                </div>
                            @endforeach
                            <div>
                                <dt>Total</dt>
                                <dd>Rs {{ formatIndianNumber($detail['total_amount']) }}</dd>
                            </div>
                            <div>
                                <dt>GST</dt>
                                <dd>+ {{ $detail['gst'] }} %</dd>
                            </div>
                            <div>
                                <dt>Ex Price</dt>
                                <dd>Rs {{ formatIndianNumber($detail['ex_price']) }}</dd>
                            </div>
                        </dl>
                    @else
                        <div class="text-center">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" wire:click="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
