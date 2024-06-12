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
                            <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form wire:submit.prevent="save()">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
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
                            <div class="col-md-4 mb-3">
                                <div>
                                    <label for="product_id" class="form-label">Product</label>
                                    <select class="form-select select2 @error('product_id') is-invalid @enderror" id="product_id" wire:model="product_id">
                                        <option value="">Select Product</option>
                                        @foreach ($product_list as $product_data)
                                            <option value="{{ $product_data->id }}">{{ $product_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('product_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <div>
                                    <label for="brand_id" class="form-label">Brand</label>
                                    <select class="form-select select2 @error('brand_id') is-invalid @enderror" id="brand_id" wire:model="brand_id">
                                        <option value="">Select Brand</option>
                                        @foreach ($brand_list as $brand_data)
                                            <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('brand_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.select2').on('change', function (e) {
                    let elementName = $(this).attr('id');
                    var data = $(this).select2("val");
                    @this.set(elementName, data);
                });
                window.addEventListener('render-select2', event => {
                    $('.select2').select2();
                })

                $('#category_id').on('change', function (e) {
                    @this.setProductList();
                });

                $('#product_id').on('change', function (e) {
                    @this.setBrandList();
                });
            });
        </script>
    @endpush
</div>
