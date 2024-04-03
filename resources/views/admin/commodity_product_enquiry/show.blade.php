<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4> {{ $page_title }} - {{ $data->unique_id }}</h4>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p>
                                <b>Product: </b> {{ $data->getCommodityProduct->name}} <br>
                                <b>Brand: </b> {{ $data->getBrand->name}} <br>
                            </p>
                        </div>
                        <div class="col-6"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
