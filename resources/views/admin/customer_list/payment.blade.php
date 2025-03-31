<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.customer_list.customer_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4 card-title">
                            <h5 class="mt-2">All Payments</h5>
                        </div>
                        <div class="col-8">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <label for="credit_availability" class="form-check-label me-1">Credit Availability</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input status_update" id="credit_availability" value="1" {{$data->credit_availability == 1 ? 'checked' : ''}}>
                                </div>
                                <label for="credit_days" class="form-check-label me-1">Credit Days</label>
                                <input type="number" class="form-control form-control-sm w-25 @error('credit_days') is-invalid @enderror" id="credit_days" wire:model.defer="credit_days" placeholder="Credit Days" min="0">
                                @error('credit_days')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <button class="btn btn-xs btn-success ms-2" wire:click="updateCreditAvailability()">Update</button>
                                {{-- <button type="button" class="btn btn-danger btn-icon-text mb-2 mb-md-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download-cloud btn-icon-prepend"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg>
                                    Download Report
                                </button> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6 text-end">
                        <a href="{{route('admin.customer-payment-list', $data->id)}}?mode=cashwallet" wire:navigate>
                            <span class="badge {{$mode == 'cashwallet' ? 'bg-success text-white' : ''}} border border-success text-success p-3">
                                <h6>Cash Balance</h6>
                                <h3> ₹ {{ formatIndianNumber($data->cash_balance) }} </h3>
                            </span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{route('admin.customer-payment-list', $data->id)}}?mode=creditwallet" wire:navigate>
                            <span class="badge {{$mode == 'creditwallet' ? 'bg-primary text-white' : ''}} border border-primary text-primary p-3">
                                <h6>Credit Balance</h6>
                                <h3> ₹ {{ formatIndianNumber($data->credit_balance) }} </h3>
                            </span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($mode == 'cashwallet')
                                    @foreach ($cash_transactions as $cash_transaction)
                                        <tr>
                                            <td>{{$cash_transaction->transaction_id}}</td>
                                            <td><b>₹ {{formatIndianNumber($cash_transaction->amount)}}</b></td>
                                            <td>{{ucfirst($cash_transaction->status)}}</td>
                                            <td>{{$cash_transaction->created_at}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if ($mode == 'creditwallet')
                                    @foreach ($credit_transactions as $credit_transaction)
                                        <tr>
                                            <td>{{$credit_transaction->transaction_id}}</td>
                                            <td><b>₹ {{formatIndianNumber($credit_transaction->amount)}}</b></td>
                                            <td>{{ucfirst($credit_transaction->status)}}</td>
                                            <td>{{$credit_transaction->created_at}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        @if ($mode == 'cashwallet')
                            {{$cash_transactions->links()}}
                        @endif
                        @if ($mode == 'creditwallet')
                            {{$credit_transactions->links()}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
