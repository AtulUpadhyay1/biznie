<div>
     @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                            <small> ( {{ $data->order_id }} ) </small>
                            <span class="badge rounded-pill border {{$data->status == 'cancel' ? 'border-danger text-danger' : 'border-primary text-primary' }} rounded-pill ms-1">{{ $data->status }} </span>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.commodity-product-order.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center ms-2" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                        <div class="col-12 text-center">
                            @include('admin.commodity_product_order.menu', ['is_active' => 'transporter'])
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h5 class="my-3">Available Transporter</h5>
                        </div>

                        <div class="col-4 text-end">
                            @if (count($this->transporter_user_id))
                                <button class="btn btn-primary btn-xs my-2" title="Send enquiry to seller" wire:click="sendTransporterEnquiry()">Send Enquiry</button>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transporter Name</th>
                                    <th>Contact Number</th>
                                    <th>Address</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transporter_list as $transporter_data)
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="transporter_{{ $transporter_data->user_id }}" class="form-check-input" value="{{ $transporter_data->user_id }}" wire:model.live="transporter_user_id">
                                        </td>
                                        <td>{{ $transporter_data->getUser->name }}</td>
                                        <td>{{ $transporter_data->getUser->phone }}</td>
                                        <td>
                                            <b>State</b> : {{ $transporter_data->state }} <br>
                                            <b>City</b> : {{ $transporter_data->city }} <br>
                                        </td>
                                        <td>
                                           {{ number_format($transporter_data->min_price) }} - {{ number_format($transporter_data->max_price) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No Transporter Available for this Order</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
