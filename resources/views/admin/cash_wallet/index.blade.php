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
                        </div>
                        <div class="col-6 text-end">
                            {{-- <a href="{{route('admin.commodity-product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a> --}}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Add Fund</h5>
                                </div>
                                <form wire:submit.prevent="save()">

                                    <div class="card-body">
                                        <div class="mb-2">
                                            <div wire:ignore>
                                                <label for="user_id" class="form-label">User</label>
                                                <select class="form-select select2 @error('user_id') is-invalid @enderror" id="user_id" wire:model="user_id">
                                                    <option value="">Select User</option>
                                                    @foreach ($user_list as $user_data)
                                                        <option value="{{ $user_data->id }}">{{ $user_data->name }} ( {{ $user_data->phone }} )</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('user_id') <small class="text-danger">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="mb-2">
                                            <label for="amount" class="form-label">Amount</label>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" placeholder="Enter Amount" wire:model="amount">
                                            @error('amount') <small class="text-danger">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="mb-2">
                                            <label for="mode" class="form-label">Mode</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="mode" id="cash" wire:model="mode" value="cash">
                                                    <label class="form-check-label" for="cash">
                                                        Cash
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="mode" id="online" wire:model="mode" value="online">
                                                    <label class="form-check-label" for="online">
                                                        Online
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="mode" id="cheque" wire:model="mode" value="cheque">
                                                    <label class="form-check-label" for="cheque">
                                                        Cheque
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="mode" id="other" wire:model="mode" value="other">
                                                    <label class="form-check-label" for="other">
                                                        Other
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" cols="30" rows="1" placeholder="Enter description" wire:model="description"></textarea>
                                            @error('description') <small class="text-danger">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" cols="30" rows="1" placeholder="Enter notes" wire:model="notes"></textarea>
                                            @error('notes') <small class="text-danger">{{ $message }}</small>@enderror
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-12 text-end">
                                                <x-submit-btn text=" Save" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>User Details</h5>
                                </div>
                                <div class="card-body">
                                    @if ($user_detail)
                                        <p><b>Name : </b> {{ $user_detail->name }} ({{ $user_detail->type }})</p>
                                        <p><b>Phone : </b> {{ $user_detail->phone }}</p>
                                        <div class="d-flex mt-2">
                                            <div class="me-4">
                                                <span class="badge border border-success text-success p-3">
                                                    <h6>Cash Balance</h6>
                                                    <h3> ₹ {{ formatIndianNumber($user_detail->cash_wallet) }} </h3>
                                                </span>
                                            </div>

                                            <div class="ms-4">
                                                <span class="badge border border-primary text-primary p-3">
                                                    <h6>Credit Balance</h6>
                                                    <h3> ₹ {{ formatIndianNumber($user_detail->credit_wallet) }} </h3>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <table class="custom-table">
                                                <thead>
                                                    <tr>
                                                        <th>Id</th>
                                                        <th>Amount</th>
                                                        <th>Transaction Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($latest_transactions as $transaction)
                                                        <tr>
                                                            <td> {{ $transaction->transaction_id }} </td>
                                                            <td>
                                                                <span class="{{$transaction->status == 'credit' ? 'text-success' : 'text-danger' }}"> ₹ {{ formatIndianNumber($transaction->amount) }}</span>
                                                            </td>
                                                            <td> {{ $transaction->transaction_status }} </td>
                                                            <td> {{ $transaction->created_at->format('Y-m-d, h:i a') }} </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-danger text-center h6">Please select an user.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.select2').on('change', function (e) {
                    let elementName = $(this).attr('id');
                    var data = $(this).select2("val");
                    @this.set(elementName, data);
                });
                window.addEventListener('render-select2', event => {
                    $('.select2').select2();
                })
            });
        </script>
    @endpush
</div>
