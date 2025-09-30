<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <a type="button" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center"
                                    title="add" href="{{ route('admin.seller-product.add', $user_id) }}"
                                    wire:navigate>
                                    <i class="bi bi-plus-lg btn-icon-prepend"></i>
                                    Add
                                </a>
                            </div>
                        </div>
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
                                            <button type="button" class="btn btn-outline-danger btn-xs btn-icon" data-bs-toggle="modal" data-bs-target="#exampleModal" title="View Calculation" wire:click="viewPriceCalculation({{ $data->id }})">
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
                                                    {{ $data->status == 'active' ? 'checked' : '' }}
                                                    wire:change="updateStatus({{ $data->id }})">
                                            </div>
                                        </td>
                                        <td>
                                            <a type="button" id="ActionBtn_{{ $data->id }}"
                                                data-bs-toggle="dropdown" role="button" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn_{{ $data->id }}">
                                                <a href="{{ route('admin.seller-product.show', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                                <a href="{{ route('admin.seller-product.edit', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                <a href="{{ route('admin.seller-product.variation', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-card-checklist icon-sm me-2"></i><span>Variant</span></a>
                                                <a href="{{ route('admin.seller-product.quality', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-cart-check icon-sm me-2"></i><span>Quality</span></a>
                                                <a href="{{ route('admin.seller-product.price', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-currency-rupee icon-sm me-2"></i><span>Update
                                                        Price</span></a>
                                                <a href="{{ route('admin.seller-product.stock', [$data->user_id, $data->id]) }}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-database icon-sm me-2"></i><span>Update
                                                        Stock</span></a>

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
                        <b>Base Price:</b> Rs {{ formatIndianNumber($detail['base_price']) }} <br>
                        <b>Guage Difference:</b> + Rs {{ formatIndianNumber($detail['default_variation_price']) }} <br>
                        @if($detail['loading_charge'] > 0)
                            <b>Loading Charge:</b> + Rs {{ formatIndianNumber($detail['loading_charge']) }} <br>
                        @endif
                        @if($detail['insurance_charge'] > 0)
                            <b>Insurance Charge:</b> + Rs {{ formatIndianNumber($detail['insurance_charge']) }} <br>
                        @endif
                        @if ($detail['quality_charge'] > 0)
                            <b>Quality Charge: </b>+ Rs {{ formatIndianNumber($detail['quality_charge']) }} <br>
                        @endif

                        @foreach ($detail['other_charges'] as $charge)
                            <b>{{ $charge['name'] }}:</b> {{ $charge['operator'] }} Rs {{ formatIndianNumber($charge['price']) }} <br>
                        @endforeach


                        <br>
                        <b>Total:</b> Rs {{ formatIndianNumber($detail['total_amount']) }} <br>
                        <b>GST:</b> + {{ $detail['gst'] }} % <br>
                        <b>Ex Price:</b> Rs {{ formatIndianNumber($detail['ex_price']) }}
                    @else
                        <div class="text-center">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-xs" data-bs-dismiss="modal" wire:click="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
