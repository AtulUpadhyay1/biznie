<div>
    @section('title', config('app.name') . ' | '.$page_title)

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add new Product</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('seller.product.index')}}" class="btn btn-primary btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-plus-lg btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
