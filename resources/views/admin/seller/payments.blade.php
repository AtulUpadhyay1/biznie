<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.seller.seller_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8 card-title">
                            <h5 class="mt-2">All Payments</h5>
                        </div>
                        <div class="col-4 text-end">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 text-end">
                        <a href="{{route('admin.seller.payments', $data->id)}}?mode=cashwallet" wire:navigate>
                            <span class="badge {{$mode == 'cashwallet' ? 'bg-success text-white' : ''}} border border-success text-success p-3">
                                <h6>Cash Balance</h6>
                                <h3> ₹ {{ formatIndianNumber($data->cash_balance) }} </h3>
                            </span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{route('admin.seller.payments', $data->id)}}?mode=creditwallet" wire:navigate>
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
                                            <td><b>RS {{$cash_transaction->amount}}</b></td>
                                            <td>{{ucfirst($cash_transaction->status)}}</td>
                                            <td>{{$cash_transaction->created_at}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if ($mode == 'creditwallet')
                                    @foreach ($credit_transactions as $credit_transaction)
                                        <tr>
                                            <td>{{$credit_transaction->transaction_id}}</td>
                                            <td><b>RS {{$credit_transaction->amount}}</b></td>
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
