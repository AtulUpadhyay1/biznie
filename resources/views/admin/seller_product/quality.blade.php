<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-secondary btn-sm" wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                </div>
            </div>
            <form wire:submit.prevent="save()">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Quality</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($quality)
                                        @foreach ($quality as $key => $item)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input quality-checkbox" type="checkbox" value="{{ $item }}" id="quality_{{ $key }}" wire:model="selected_quality" data-key="{{ $key }}">
                                                        <label class="form-check-label visually-hidden" for="quality_{{ $key }}">{{ $item }}</label>
                                                    </div>
                                                </td>
                                                <td>{{ $item }}</td>
                                                <td>
                                                    <label class="form-label visually-hidden" for="price_{{ $key }}">{{ $item }} Price</label>
                                                    <input type="number" class="form-control price-input" wire:model="selected_quality_price.{{ $item }}" placeholder="Enter Price" min="0" step="0.01" id="price_{{ $key }}">
                                                    @error('selected_quality_price.'.$item) <span class="text-danger">{{ $message }}</span> @enderror
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <x-table-no-data colspan="3" title="No quality found" text="This product has no quality grades configured." />
                                    @endif
                                </tbody>
                            </table>
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
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle checkbox change events
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('quality-checkbox')) {
                    const key = e.target.getAttribute('data-key');
                    const priceInput = document.getElementById('price_' + key);

                    if (e.target.checked) {
                        priceInput.disabled = false;
                        // Set default price if no price is currently set
                        if (!priceInput.value || priceInput.value === '') {
                            const defaultPrices = @json($quality_price);
                            if (defaultPrices[key] !== undefined && defaultPrices[key] !== '') {
                                // Use Livewire to set the value
                                @this.set('selected_quality_price.' + e.target.value, defaultPrices[key]);
                            }
                        }
                        priceInput.focus();
                    } else {
                        priceInput.disabled = true;
                        // Clear the price using Livewire
                        @this.set('selected_quality_price.' + e.target.value, '');
                    }
                }
            });
        });
    </script>
</div>
