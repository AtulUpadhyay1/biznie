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

                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->getCommodityProductVariation as $variation_key => $variation)
                                        <tr>
                                            <th><span class="badge bg-danger">{{ $loop->iteration }}</span></th>
                                            @foreach ($selected_attributes as $attribute)
                                                <td>
                                                    <input type="text" class="form-control form-control-sm @error('uploaded_variation.'.getAttribute($attribute)->name) is-invalid @enderror" placeholder="Enter {{getAttribute($attribute)->name}}" wire:model="uploaded_variation.{{$variation->id}}.{{getAttribute($attribute)->name}}">
                                                    @error('uploaded_variation.'.getAttribute($attribute)->name) <small class="text-danger">{{ $message }}</small>@enderror
                                                </td>
                                            @endforeach

                                            <td>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-inverse-danger btn-sm btn-icon" wire:click="removeVariation({{$variation->id}})" title="Remove Variation"><i class="bi bi-x-circle"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <th><span class="badge bg-danger">N</span></th>
                                        @foreach ($selected_attributes as $attribute)
                                            <td>
                                                <input type="text" class="form-control form-control-sm @error('variation.'.getAttribute($attribute)->name) is-invalid @enderror" placeholder="Enter {{getAttribute($attribute)->name}}" wire:model="variation.{{getAttribute($attribute)->name}}">
                                                @error('variation.'.getAttribute($attribute)->name) <small class="text-danger">{{ $message }}</small>@enderror
                                            </td>
                                        @endforeach

                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div>
                                <button type="button" class="btn btn-inverse-success btn-sm py-1" wire:click="addVariation(false)" title="Add Field">Add More</button>
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
