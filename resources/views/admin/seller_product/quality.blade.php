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
                            <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
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
                                                    </div>
                                                </td>
                                                <td>{{ $item }}</td>
                                                <td>
                                                    <input type="number" class="form-control price-input" wire:model="selected_quality_price.{{ $item }}" placeholder="Enter Price" min="0" step="0.01" id="price_{{ $key }}">
                                                    @error('selected_quality_price.'.$item) <span class="text-danger">{{ $message }}</span> @enderror
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">No quality found.</td>
                                        </tr>
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
