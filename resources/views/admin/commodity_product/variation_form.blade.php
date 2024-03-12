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
                            <a href="{{route('admin.commodity-product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @foreach ($selected_attributes as $attribute)
                                            <th class="text-center">
                                                {{getAttribute($attribute)->name}} <br> <hr style="margin: 3px; border: 0; border-top: 1px solid; opacity: 1.1;">
                                                <select class="form-control form-control-sm text-center" wire:model="unit.{{getAttribute($attribute)->name}}">
                                                    <option value="">Select Unit</option>
                                                    @foreach ($unit_list as $unit_data)
                                                        <option value="{{$unit_data->id}}">{{$unit_data->name}} ({{$unit_data->short_name}})</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                        @endforeach
                                        {{-- <th class="text-center">Price</th> --}}
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th><span class="badge bg-danger">1</span></th>
                                        @foreach ($selected_attributes as $attribute)
                                            <td>
                                                <input type="text" class="form-control form-control-sm @error('variation.'.getAttribute($attribute)->name.'.0') is-invalid @enderror" placeholder="Enter {{getAttribute($attribute)->name}}" wire:model="variation.{{getAttribute($attribute)->name}}.0">
                                                @error('variation.'.getAttribute($attribute)->name.'.0') <small class="text-danger">{{ $message }}</small>@enderror
                                            </td>
                                        @endforeach
                                        {{-- <td>
                                            <input type="number" class="form-control form-control-sm @error('variation.Price.0') is-invalid @enderror" placeholder="Enter Price" wire:model="variation.Price.0">
                                            @error('variation.Price.0') <small class="text-danger">{{ $message }}</small>@enderror
                                        </td> --}}
                                        <td></td>
                                    </tr>
                                    @foreach ($variation_inputs as $variation_key => $variation_input)
                                        <tr>
                                            <th><span class="badge bg-danger">{{ $loop->iteration+1 }}</span></th>
                                            @foreach ($selected_attributes as $attribute)
                                                <td>
                                                    <input type="text" class="form-control form-control-sm @error('variation.'.getAttribute($attribute)->name.'.'.$variation_input) is-invalid @enderror" placeholder="Enter {{getAttribute($attribute)->name}}" wire:model="variation.{{getAttribute($attribute)->name}}.{{$variation_input}}">
                                                    @error('variation.'.getAttribute($attribute)->name.'.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                                </td>
                                            @endforeach
                                            {{-- <td>
                                                <input type="number" class="form-control form-control-sm @error('variation.Price.'.$variation_input) is-invalid @enderror" placeholder="Enter Price" wire:model="variation.Price.{{$variation_input}}">
                                                @error('variation.Price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                            </td> --}}
                                            <td>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-inverse-danger btn-sm btn-icon" wire:click="removeVariationField({{$variation_key}})" title="Remove Field"><i class="bi bi-x-circle"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div>
                                <button type="button" class="btn btn-inverse-success btn-sm py-1" wire:click="addVariationField({{$variation_count}}, false)" title="Add Field">Add More</button>
                            </div>
                        </div>

                        {{-- <div class="row">
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

                            <div class="col-md-6 mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Dimension</span>
                                    <input type="text" class="form-control @error('dimension.0') is-invalid @enderror" placeholder="Enter Dimension" wire:model="dimension.0">
                                    <span class="input-group-text">Price</span>
                                    <input type="number" class="form-control @error('dimension_price.0') is-invalid @enderror" placeholder="Enter Dimension Price" wire:model="dimension_price.0">
                                </div>
                                @error('dimension.0') <small class="text-danger">{{ $message }}</small>@enderror
                                @error('dimension_price.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group mb-3">
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

                                    @if ($variation == $variation_key+1)
                                        <button type="button" class="btn btn-inverse-success btn-sm py-1" wire:click="addVariationField({{$variation}}, false)">Add</button>
                                    @endif

                                    @error('size.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('size_price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Dimension</span>
                                        <input type="text" class="form-control @error('dimension.'.$variation_input) is-invalid @enderror" placeholder="Enter Dimension" wire:model="dimension.{{$variation_input}}">
                                        <span class="input-group-text">Price</span>
                                        <input type="number" class="form-control @error('dimension_price.'.$variation_input) is-invalid @enderror" placeholder="Enter Dimension Price" wire:model="dimension_price.{{$variation_input}}">
                                    </div>
                                    @error('dimension.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('dimension_price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

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

                            <div style="margin-top: -30px;">
                                <button type="button" class="btn btn-inverse-success btn-sm py-1" wire:click="addVariationField({{$variation}}, false)">Add More</button>
                            </div>

                        </div> --}}
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
