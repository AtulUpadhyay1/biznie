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
                            <p class="text-muted mb-0">
                                {{ $seller_name ?: '—' }}@if($product) &middot; {{ $product->name }}@endif
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="save()">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted">
                            Freight to one city. The buyer's F.O.R price is this listing's Ex Price
                            plus this rate; cities left unquoted fall back to the cheapest
                            transporter rate.
                        </p>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                <select class="form-select" id="state" wire:model.live="state">
                                    <option value="">Select State</option>
                                    @foreach ($state_list as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                                @error('state') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <select class="form-select" id="city" wire:model.live="city" @disabled(!$state)>
                                    <option value="">{{ $state ? 'Select City' : 'Select State First' }}</option>
                                    @foreach ($city_list as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                                @error('city') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Freight per MT (&#8377;) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price" wire:model="price" placeholder="Enter Freight" min="0" step="0.01">
                                @error('price') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <x-submit-btn text="Save" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h4>Quoted Cities</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Seller</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Ex Price</th>
                                    <th>Freight / MT</th>
                                    <th>F.O.R Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <th>{{ $key + 1 }}</th>
                                        <td>{{ $seller_name ?: '—' }}</td>
                                        <td>{{ $data->state }}</td>
                                        <td>{{ $data->city }}</td>
                                        <td class="text-muted">&#8377;{{ number_format($ex_price, 2) }}</td>
                                        <td class="text-muted">+ &#8377;{{ number_format($data->price, 2) }}</td>
                                        <td class="fw-bold">&#8377;{{ number_format($ex_price + $data->price, 2) }}</td>
                                        <td>
                                            <a role="button" class="text-danger for-price-delete"
                                                data-id="{{ $data->id }}" data-city="{{ $data->city }}"
                                                title="Remove">
                                                <i class="bi bi-trash icon-sm"></i>
                                            </a>
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

    {{-- Removing a quote sends that city's buyers back to the calculated F.O.R
         price, which can be a very different number — so it asks first, using
         the SweetAlert the rest of the panel already uses rather than a browser
         confirm box.

         `@script` registers once per component and survives wire:navigate, and
         the handler is delegated so rows Livewire re-renders keep working. --}}
    @script
    <script>
        $wire.$el.addEventListener('click', (e) => {
            const trigger = e.target.closest('.for-price-delete');
            if (!trigger) return;

            e.preventDefault();

            Swal.fire({
                title: 'Remove F.O.R price?',
                text: 'Buyers in ' + trigger.dataset.city + ' will go back to the calculated F.O.R price (Ex Price + freight).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.delete(trigger.dataset.id);
                }
            });
        });
    </script>
    @endscript
</div>
