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
                            <a href="{{route('seller.product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter product name">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" cols="30" rows="5" placeholder="Enter product description"></textarea>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">
                    <h5>General setup</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select @error('product_category_id') is-invalid @enderror" id="category" wire:model="category_id" wire:change="setSubCategoryList()">
                                <option>Select Category</option>
                                @foreach ($category_list as $category_data)
                                    <option value="{{ $category_data->id }}">{{ $category_data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sub_category" class="form-label">Sub Category</label>
                            <select class="form-select @error('sub_category') is-invalid @enderror" id="sub_category" wire:model="sub_category_id" wire:change="setSubSubCategoryList()">
                                <option>Select Sub Category</option>
                                @foreach ($sub_category_list as $sub_category_data)
                                    <option value="{{$sub_category_data->id}}">{{$sub_category_data->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sub_sub_category" class="form-label">Sub Sub Category</label>
                            <select class="form-select" id="sub_sub_category">
                                <option>Select Sub Sub Category</option>
                                @foreach ($sub_sub_category_list as $sub_sub_category_data)
                                    <option value="{{$sub_sub_category_data->id}}">{{$sub_sub_category_data->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <select class="form-select" id="brand">
                                <option>Select Brand</option>
                                @foreach ($brand_list as $brand_data)
                                    <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <select class="form-select" id="unit">
                                <option>Select Unit</option>
                                @foreach ($unit_list as $unit_data)
                                    <option value="{{ $unit_data->id }}">{{ $unit_data->short_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="search_tags" class="form-label">Search Tags</label>
                            <input type="text" class="form-control" id="search_tags" placeholder="Enter search tags">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Pricing & others</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="purchase_price" class="form-label">Purchase Price</label>
                            <input type="number" class="form-control" id="purchase_price" min="0" step="0.01" placeholder="Enter purchase price">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="unit_price" class="form-label">Unit Price</label>
                            <input type="number" class="form-control" id="unit_price" min="0" step="0.01" placeholder="Enter unit price">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="minimum_order_qty" class="form-label">Minimum Order Qty</label>
                            <input type="number" class="form-control" id="minimum_order_qty" min="1" step="1" placeholder="Enter minimum order qty">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="current_stock_qty" class="form-label">Current Stock Qty</label>
                            <input type="number" class="form-control" id="current_stock_qty" min="0" step="1" placeholder="Enter current stock qty">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="discount_type" class="form-label">Discount Type</label>
                            <select class="form-select" id="discount_type">
                                <option value="flat">Flat</option>
                                <option value="percent">Percent</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="current_stock_qty" class="form-label">Discount Amount</label>
                            <input type="number" class="form-control" id="current_stock_qty" min="0" step="0.01" placeholder="Enter current stock qty">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="current_stock_qty" class="form-label">Tax Amount(%)</label>
                            <input type="number" class="form-control" id="current_stock_qty" min="0" step="0.01" placeholder="Enter current stock qty">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tax_calculation" class="form-label">Tax Calculation</label>
                            <select class="form-select" id="tax_calculation">
                                <option value="include">Include with product</option>
                                <option value="exclude">Exclude with product</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="shipping_cost" class="form-label">Shipping Cost</label>
                            <input type="number" class="form-control" id="shipping_cost" min="0" step="1" placeholder="Enter shipping cost">
                        </div>

                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Product video</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="youtube_link" class="form-label">Youtube Video Link </label>
                            <span class="text-info"> (Optional please provide embed link not direct link.)</span>
                            <input type="text" class="form-control" id="youtube_link" placeholder="Enter youtube video embed link">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Seo section</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" placeholder="Enter meta title">
                            </div>
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="meta_description" cols="30" rows="5" placeholder="Enter meta description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <img src="{{asset('common/images/upload.png')}}" class="float-end">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
