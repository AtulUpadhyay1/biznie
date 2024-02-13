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
                            <a href="{{route('seller.commodity-product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="update()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter product name" wire:model="name">
                                @error('name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <x-submit-btn text="Update" />
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
