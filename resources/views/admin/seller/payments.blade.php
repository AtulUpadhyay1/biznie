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
                        <div class="col-4 card-title">
                            <h5 class="mt-2">All Payments</h5>
                        </div>
                        <div class="col-8">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <label for="credit_availability" class="form-check-label me-1">Credit Availability</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input status_update" id="credit_availability" wire:model="credit_availability" value="1" {{$data->credit_availability == 1 ? 'checked' : ''}}>
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
                        <a href="{{route('admin.seller.payments', $data->id)}}?mode=cashwallet" wire:navigate>
                            <span class="badge {{$mode == 'cashwallet' ? 'bg-success text-white' : ''}} border border-success text-success p-3">
                                <h6>Cash Balance</h6>
                                <h3> ₹ {{ formatIndianNumber($data->cash_balance) }} </h3>
                            </span>
                        </a>
                        @if ($mode == 'cashwallet')
                            <br>
                            <button class="btn btn-xs btn-outline-success mt-2" data-bs-toggle="modal" data-bs-target="#addBalance">Add Balance</button>
                        @endif
                    </div>
                    <div class="col-6">
                        <a href="{{route('admin.seller.payments', $data->id)}}?mode=creditwallet" wire:navigate>
                            <span class="badge {{$mode == 'creditwallet' ? 'bg-primary text-white' : ''}} border border-primary text-primary p-3">
                                <h6>Credit Balance</h6>
                                <h3> ₹ {{ formatIndianNumber($data->credit_balance) }} </h3>
                            </span>
                        </a>
                        @if ($mode == 'creditwallet')
                            <br>
                            <button class="btn btn-xs btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#addBalance">Add Balance</button>
                        @endif
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
                                    <th>Description / Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($mode == 'cashwallet')
                                    @foreach ($cash_transactions as $cash_transaction)
                                        <tr>
                                            <td>{{$cash_transaction->transaction_id}}</td>
                                            <td><b>₹ {{formatIndianNumber($cash_transaction->amount)}}</b></td>
                                            <td>
                                                @if(strtolower($cash_transaction->status) == 'credit')
                                                    <span class="badge bg-success text-white">Credit</span>
                                                @elseif(strtolower($cash_transaction->status) == 'debit')
                                                    <span class="badge bg-danger text-white">Debit</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">{{ ucfirst($cash_transaction->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{$cash_transaction->created_at}}</td>
                                            <td>
                                                <b>Description : </b>{{$cash_transaction->description}}
                                                @if ($cash_transaction->notes)
                                                    <hr>
                                                    <button class="btn btn-xs btn-info" type="button" data-bs-toggle="modal" data-bs-target="#notesModal-{{$cash_transaction->id}}">
                                                        View Notes
                                                    </button>

                                                    <!-- Notes Modal -->
                                                    <div class="modal fade" id="notesModal-{{$cash_transaction->id}}" tabindex="-1" aria-labelledby="notesModalLabel-{{$cash_transaction->id}}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="notesModalLabel-{{$cash_transaction->id}}">Notes</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>{{($cash_transaction->notes)}}</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if ($mode == 'creditwallet')
                                    @foreach ($credit_transactions as $credit_transaction)
                                        <tr>
                                            <td>{{$credit_transaction->transaction_id}}</td>
                                            <td><b>₹ {{formatIndianNumber($credit_transaction->amount)}}</b></td>
                                            <td>
                                                @if(strtolower($credit_transaction->status) == 'credit')
                                                    <span class="badge bg-success text-white">Credit</span>
                                                @elseif(strtolower($credit_transaction->status) == 'debit')
                                                    <span class="badge bg-danger text-white">Debit</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">{{ ucfirst($credit_transaction->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{$credit_transaction->created_at}}</td>
                                            <td>
                                                {{$credit_transaction->description}}

                                                <b>Description : </b>{{$credit_transaction->description}}
                                                @if ($credit_transaction->notes)
                                                    <hr>
                                                    <button class="btn btn-xs btn-info" type="button" data-bs-toggle="modal" data-bs-target="#notesModal-{{$credit_transaction->id}}">
                                                        View Notes
                                                    </button>

                                                    <!-- Notes Modal -->
                                                    <div class="modal fade" id="notesModal-{{$credit_transaction->id}}" tabindex="-1" aria-labelledby="notesModalLabel-{{$credit_transaction->id}}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="notesModalLabel-{{$credit_transaction->id}}">Notes</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>{{($credit_transaction->notes)}}</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                            </td>
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
    <div class="modal fade" id="addBalance" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addBalanceLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBalanceLabel">Add Balance In {{ $mode == 'cashwallet' ? 'Cash Wallet' : 'Credit Wallet' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="amount" placeholder="Enter amount" wire:model="amount" required>
                        @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="mode" class="form-label">Mode</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode" id="mode_cash" value="cash" wire:model="payment_method" required>
                                <label class="form-check-label" for="mode_cash">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode" id="mode_online" value="online" wire:model="payment_method">
                                <label class="form-check-label" for="mode_online">Online</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode" id="mode_cheque" value="cheque" wire:model="payment_method">
                                <label class="form-check-label" for="mode_cheque">Cheque</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode" id="mode_other" value="other" wire:model="payment_method">
                                <label class="form-check-label" for="mode_other">Other</label>
                            </div>
                            @if ($mode == 'creditwallet')
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mode" id="cash_balance" value="cash_balance" wire:model="payment_method">
                                    <label class="form-check-label" for="cash_balance">Cash Balance</label>
                                </div>
                            @endif
                        </div>
                        @error('mode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="2" placeholder="Enter description" wire:model="description" data-max-words="100"></textarea>
                        <div class="form-text text-muted">Words: <span id="description_word_count">0</span>/100</div>
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" rows="3" placeholder="Enter notes" wire:model="notes" data-max-words="2000"></textarea>
                        <div class="form-text text-muted">Words: <span id="notes_word_count">0</span>/2000</div>
                        @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-xs btn-primary" wire:click="{{ $mode == 'cashwallet' ? 'addCashWalletBalanace()' : 'addCreditWalletBalanace()' }}">Add Balance</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function(){
            // Returns number of words in a string
            function countWords(str){
                if(!str) return 0;
                return str.trim().split(/\s+/).filter(function(w){ return w.length > 0; }).length;
            }

            function enforceAndCount(textarea){
                var max = parseInt(textarea.dataset.maxWords, 10) || 0;
                var val = textarea.value || '';
                var words = val.trim().split(/\s+/).filter(function(w){ return w.length > 0; });
                if(max > 0 && words.length > max){
                    // trim to allowed words
                    var trimmed = words.slice(0, max).join(' ');
                    textarea.value = trimmed;
                    // move caret to end
                    textarea.selectionStart = textarea.selectionEnd = textarea.value.length;
                    // notify Livewire/other listeners
                    var ev = new Event('input', { bubbles: true });
                    textarea.dispatchEvent(ev);
                    words = words.slice(0, max);
                }
                // update counter element if present
                var counterId = textarea.id + '_word_count';
                var counterEl = document.getElementById(counterId);
                if(counterEl){
                    counterEl.textContent = words.length;
                }
            }

            function attach(el){
                if(!el) return;
                // initial count
                enforceAndCount(el);
                el.addEventListener('input', function(){ enforceAndCount(el); });
            }

            document.addEventListener('DOMContentLoaded', function(){
                var textareas = document.querySelectorAll('textarea[data-max-words]');
                textareas.forEach(function(t){ attach(t); });
            });

            // If modal is dynamically shown, re-evaluate counts when opened (Bootstrap event)
            var addBalanceModal = document.getElementById('addBalance');
            if(addBalanceModal){
                addBalanceModal.addEventListener('shown.bs.modal', function(){
                    var textareas = addBalanceModal.querySelectorAll('textarea[data-max-words]');
                    textareas.forEach(function(t){ enforceAndCount(t); });
                });
            }
        })();
    </script>
</div>
