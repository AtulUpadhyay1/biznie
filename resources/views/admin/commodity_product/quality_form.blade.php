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
                        <div class="row">
                            <div class="col-md-11 mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Quality</span>
                                    <input type="text" class="form-control @error('quality.0') is-invalid @enderror" placeholder="Enter Quality" wire:model="quality.0">
                                    <span class="input-group-text">Price</span>
                                    <input type="number" class="form-control @error('quality_price.0') is-invalid @enderror" placeholder="Enter Quality Price" wire:model="quality_price.0">
                                </div>
                                @error('quality.0') <small class="text-danger">{{ $message }}</small>@enderror
                                @error('quality_price.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-1 mb-3">
                                <button type="button" class="btn btn-inverse-success" wire:click="addQualityField({{$quality_field}})">Add</button>
                            </div>

                            @foreach ($quality_inputs as $quality_key => $quality_input)

                                <div class="col-md-11 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Quality</span>
                                        <input type="text" class="form-control @error('quality.'.$quality_input) is-invalid @enderror" placeholder="Enter Quality" wire:model="quality.{{$quality_input}}">
                                        <span class="input-group-text">Price</span>
                                        <input type="number" class="form-control @error('quality_price.'.$quality_input) is-invalid @enderror" placeholder="Enter Quality Price" wire:model="quality_price.{{$quality_input}}">
                                    </div>
                                    @error('quality.'.$quality_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('quality_price.'.$quality_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-1 mb-3">
                                    <button type="button" class="btn btn-inverse-danger" wire:click="removeQualityField({{$quality_key}})">Remove</button>
                                </div>

                            @endforeach
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
    </div>
</div>
