<div>
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
                            <a href="{{route('admin.commodity-product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text">Size</span>
                                    <input type="text" class="form-control @error('size.0') is-invalid @enderror" placeholder="Enter Size" wire:model="size.0">
                                    <span class="input-group-text">Price</span>
                                    <input type="number" class="form-control @error('size_price.0') is-invalid @enderror" placeholder="Enter Size Price" wire:model="size_price.0">
                                </div>

                                @if ($variation == 0)
                                    <button type="button" class="btn btn-inverse-success btn-sm py-1" wire:click="addVariationField({{$variation}}, false)">Add</button>
                                @endif

                                @error('size.0') <small class="text-danger">{{ $message }}</small>@enderror
                                @error('size_price.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            {{--<div class="col-md-6 mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Dimension</span>
                                    <input type="text" class="form-control @error('dimension.0') is-invalid @enderror" placeholder="Enter Dimension" wire:model="dimension.0">
                                    <span class="input-group-text">Price</span>
                                    <input type="number" class="form-control @error('dimension_price.0') is-invalid @enderror" placeholder="Enter Dimension Price" wire:model="dimension_price.0">
                                </div>
                                @error('dimension.0') <small class="text-danger">{{ $message }}</small>@enderror
                                @error('dimension_price.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div> --}}

                            <div class="col-md-6 mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Specification</span>
                                    <textarea class="form-control @error('specification.0') is-invalid @enderror" wire:model="specification.0" rows="1"></textarea>
                                </div>
                                @error('specification.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            {{-- <div class="col-md-1 mb-3">
                                <button type="button" class="btn btn-inverse-success" wire:click="addVariationField({{$variation}})">Add</button>
                            </div> --}}

                            @foreach ($variation_inputs as $variation_key => $variation_input)
                                <div class="col-md-5 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Size</span>
                                        <input type="text" class="form-control @error('size.'.$variation_input) is-invalid @enderror" placeholder="Enter Size" wire:model="size.{{$variation_input}}">
                                        <span class="input-group-text">Price</span>
                                        <input type="number" class="form-control @error('size_price.'.$variation_input) is-invalid @enderror" placeholder="Enter Size Price" wire:model="size_price.{{$variation_input}}">
                                    </div>

                                    @if ($variation == $variation_key+1)
                                        <button type="button" class="btn btn-inverse-success btn-sm py-1" wire:click="addVariationField({{$variation}}, false)">Add</button>
                                    @endif

                                    @error('size.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('size_price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                {{-- <div class="col-md-6 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Dimension</span>
                                        <input type="text" class="form-control @error('dimension.'.$variation_input) is-invalid @enderror" placeholder="Enter Dimension" wire:model="dimension.{{$variation_input}}">
                                        <span class="input-group-text">Price</span>
                                        <input type="number" class="form-control @error('dimension_price.'.$variation_input) is-invalid @enderror" placeholder="Enter Dimension Price" wire:model="dimension_price.{{$variation_input}}">
                                    </div>
                                    @error('dimension.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('dimension_price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div> --}}

                                <div class="col-md-6 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Specification</span>
                                        <textarea class="form-control @error('specification.'.$variation_input) is-invalid @enderror" wire:model="specification.{{$variation_input}}" rows="1"></textarea>
                                    </div>
                                    @error('specification.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-1 mb-3">
                                    <button type="button" class="btn btn-inverse-danger" wire:click="removeVariationField({{$variation_key}})">Remove</button>
                                </div>
                            @endforeach
                        </div>
                        {{-- {{$variation}} {{ count($variation_inputs)}} --}}
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <x-submit-btn text="Save" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
