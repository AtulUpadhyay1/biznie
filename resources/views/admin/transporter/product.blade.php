<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.transporter.index') }}" wire:navigate>
                            <i class="bi bi-arrow-left"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="assignProduct()">
                        <div class="row">
                            @foreach ($commodity_list as $commodity_data)
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <input type="checkbox" class="form-check-input" id="product_{{$commodity_data->id}}" wire:model="commodity_product" value="{{$commodity_data->id}}" style="position: absolute;">
                                        <img src="{{ imageUrl($commodity_data->thumbnail) }}" class="card-img-top" onerror="this.onerror=null; this.src='{{ asset('admin_css/no-photo.png') }}'">
                                        <div class="card-body">
                                            <p><b>Name : </b>{{ $commodity_data->name }}</p>
                                            <p><b>Category : </b>{{ $commodity_data->getCategory->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="Assign Product" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
