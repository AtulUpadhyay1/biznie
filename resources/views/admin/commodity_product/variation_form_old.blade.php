<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{route('admin.commodity-product.index')}}" class="btn btn-secondary btn-sm" wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><span class="badge bg-light text-dark">1</span>&nbsp; Size</span>
                                    <input type="text" class="form-control @error('size.0') is-invalid @enderror" placeholder="Enter Size" wire:model="size.0">
                                    <span class="input-group-text">Price</span>
                                    <input type="number" class="form-control @error('size_price.0') is-invalid @enderror" placeholder="Enter Size Price" wire:model="size_price.0">
                                </div>

                                @error('size.0') <small class="text-danger">{{ $message }}</small>@enderror
                                @error('size_price.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            {{--<div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text">Dimension</span>
                                    <input type="text" class="form-control @error('dimension.0') is-invalid @enderror" placeholder="Enter Dimension" wire:model="dimension.0">
                                    <span class="input-group-text">Price</span>
                                    <input type="number" class="form-control @error('dimension_price.0') is-invalid @enderror" placeholder="Enter Dimension Price" wire:model="dimension_price.0">
                                </div>
                                @error('dimension.0') <small class="text-danger">{{ $message }}</small>@enderror
                                @error('dimension_price.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div> --}}

                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text">Specification</span>
                                    <textarea class="form-control @error('specification.0') is-invalid @enderror" wire:model="specification.0" rows="1"></textarea>
                                </div>
                                @error('specification.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            @foreach ($variation_inputs as $variation_key => $variation_input)
                                <div class="col-md-5 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><span class="badge bg-light text-dark">{{ $loop->iteration+1 }}</span>&nbsp; Size</span>
                                        <input type="text" class="form-control @error('size.'.$variation_input) is-invalid @enderror" placeholder="Enter Size" wire:model="size.{{$variation_input}}">
                                        <span class="input-group-text">Price</span>
                                        <input type="number" class="form-control @error('size_price.'.$variation_input) is-invalid @enderror" placeholder="Enter Size Price" wire:model="size_price.{{$variation_input}}">
                                    </div>

                                    {{-- @if ($variation == $variation_key+1)
                                        <button type="button" class="btn btn-inverse-success btn-sm py-1" wire:click="addVariationField({{$variation}}, false)">Add</button>
                                    @endif --}}

                                    @error('size.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('size_price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                {{-- <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Dimension</span>
                                        <input type="text" class="form-control @error('dimension.'.$variation_input) is-invalid @enderror" placeholder="Enter Dimension" wire:model="dimension.{{$variation_input}}">
                                        <span class="input-group-text">Price</span>
                                        <input type="number" class="form-control @error('dimension_price.'.$variation_input) is-invalid @enderror" placeholder="Enter Dimension Price" wire:model="dimension_price.{{$variation_input}}">
                                    </div>
                                    @error('dimension.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('dimension_price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div> --}}

                                <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Specification</span>
                                        <textarea class="form-control @error('specification.'.$variation_input) is-invalid @enderror" wire:model="specification.{{$variation_input}}" rows="1"></textarea>
                                    </div>
                                    @error('specification.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-1 mb-3">
                                    <button type="button" class="btn btn-sm btn-inverse-danger" wire:click="removeVariationField({{$variation_key}})"><i class="bi bi-trash"></i>Remove</button>
                                </div>
                            @endforeach

                            <div class="col-12 mb-3">
                                <button type="button" class="btn btn-sm btn-inverse-success" wire:click="addVariationField({{$variation}}, false)"><i class="bi bi-plus-lg"></i>Add More</button>
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
                </form>
            </div>
        </div>
    </div>
</div>
