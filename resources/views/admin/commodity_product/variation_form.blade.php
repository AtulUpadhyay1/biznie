<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{route('admin.commodity-product.index')}}" class="btn btn-secondary btn-sm" wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
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
                                                {{getAttribute($attribute)->name}} <br><hr>
                                                <select class="form-select form-select-sm text-center" wire:model="unit.{{getAttribute($attribute)->name}}" wire:change="updateUnit()">
                                                    <option value="">Select Unit</option>
                                                    @foreach ($unit_list as $unit_data)
                                                        <option value="{{$unit_data->id}}">{{$unit_data->name}} ({{$unit_data->short_name}})</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                        @endforeach
                                        <th class="col-1 text-center">Default</th>
                                        <th class="col-1 text-center">Action</th>
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
                                                <div class="text-center">
                                                    @if ($variation->is_default == 1)
                                                        <span class="bz-status bz-status--info">Default</span>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-inverse-primary btn-icon" wire:click="makeDefaultVariation({{$variation->id}})" title="Make Default"><i class="bi bi-check2-circle"></i></button>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-sm btn-inverse-danger btn-icon" wire:click="removeVariation({{$variation->id}})" title="Remove Variation"><i class="bi bi-x-circle"></i></button>
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

                                        <td colspan="2"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div>
                                <button type="button" class="btn btn-sm btn-inverse-success" wire:click="addVariation(false)" title="Add Field"><i class="bi bi-plus-lg"></i>Add More</button>
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
