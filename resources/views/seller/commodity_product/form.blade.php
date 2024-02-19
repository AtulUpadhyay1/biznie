<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <style>
        .select2-container--default .select2-selection--single{
            height: 42px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }
    </style>
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
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div wire:ignore>
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select select2 @error('category_id') is-invalid @enderror" id="category_id" wire:model="category_id">
                                        <option value="">Select Category</option>
                                        @foreach ($category_list as $category_data)
                                            <option value="{{ $category_data->id }}">{{ $category_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div>
                                    <label for="sub_category" class="form-label">Sub Category</label>
                                    <select class="form-select select2 sub_category @error('sub_category_id') is-invalid @enderror" id="sub_category" wire:model="sub_category_id">
                                        <option>Select Sub Category</option>
                                        @foreach ($sub_category_list as $sub_category_data)
                                            <option value="{{$sub_category_data->id}}">{{$sub_category_data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('sub_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sub_sub_category" class="form-label">Sub Sub Category</label>
                                <select class="form-select select2 @error('sub_sub_category_id') is-invalid @enderror" id="sub_sub_category" wire:model="sub_sub_category_id">
                                    <option>Select Sub Sub Category</option>
                                    @foreach ($sub_sub_category_list as $sub_sub_category_data)
                                        <option value="{{$sub_sub_category_data->id}}">{{$sub_sub_category_data->name}}</option>
                                    @endforeach
                                </select>
                                @error('sub_sub_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3" wire:ignore>
                                <label for="brand_id" class="form-label">Brand</label>
                                <select class="form-select select2 @error('brand_id') is-invalid @enderror" id="brand_id" wire:model="brand_id">
                                    <option>Select Brand</option>
                                    @foreach ($brand_list as $brand_data)
                                        <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-12 mb-3" wire:ignore>
                                <label for="product_id" class="form-label">Product</label>
                                <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" wire:model="product_id">
                                    <option data-image="{{asset('seller_css/no-photo.png')}}" value="">Select Product</option>
                                    @foreach ($product_list as $product_data)
                                        <option value="{{ $product_data->id }}" data-image="{{ imageUrl($product_data->thumbnail) }}">{{ $product_data->name }}</option>
                                    @endforeach
                                </select>
                                @error('product_id') <small class="text-danger">{{ $message }}</small>@enderror
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
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#product_id').select2({
                    templateResult: formatProduct,
                    templateSelection: formatProductSelection
                });

                // Custom formatting for displaying product images in the dropdown
                function formatProduct(product) {
                    if (!product.id) {
                        return product.text;
                    }

                    var $product = $(
                        '<span><img style="width:30px;height:30px;border-radius:50%;margin-right:10px;" src="' + $(product.element).data('image') + '" class="img-flag" /> ' + product.text + '</span>'
                    );
                    return $product;
                }

                function formatProductSelection(product) {
                    return product.text;
                }

                $('#product_id').on('change', function() {
                    @this.set('product_id', $(this).val());
                });

                $('#category_id').on('change', function (e) {
                    @this.set('category_id', $(this).val());
                    @this.setSubCategoryList();
                });

                $('#sub_category').on('change', function (e) {
                    @this.setSubSubCategoryList();
                });
            });
        </script>
    @endpush
</div>
