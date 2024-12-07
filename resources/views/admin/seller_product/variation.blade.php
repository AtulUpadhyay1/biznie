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
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="selectAll" wire:click="allSelect({{$selectAll}})" @if($is_select_all) checked @endif>
                                                <label class="form-check-label" for="selectAll">
                                                    Select All
                                                </label>
                                            </div>
                                        </th>
                                        @php
                                            $attributes = $list[0]->value;
                                        @endphp
                                        @foreach ($attributes as $attribute)
                                            <th>
                                                {{$attribute['name']}}
                                            </th>
                                        @endforeach
                                        <th>Guage Difference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $data)
                                        <tr>
                                            <th>
                                                <div class="form-check">
                                                    <label class="form-check-label" for="exampleCheck{{$loop->iteration}}">
                                                        {{ $loop->iteration }}
                                                    </label>
                                                    <input type="checkbox" class="form-check-input" id="exampleCheck{{$loop->iteration}}" wire:model="variation_price.{{$data->id}}.is_selected" value="1" @if($variation_price[$data->id]['is_selected']) checked @endif wire:click="checkSelectAll()">
                                                </div>
                                                {{-- @if($data->is_selected)
                                                    <i class="bi bi-check2-circle text-success fs-5"></i>
                                                @endif --}}
                                            </th>
                                            @foreach ($data->value as $price_value)
                                                <td> {{ $price_value['value'] }} </td>
                                            @endforeach
                                            <td>
                                                <input type="number" class="form-control form-control-sm @error('variation_price.'.$data->id.'.price') is-invalid @enderror" wire:model="variation_price.{{$data->id}}.price">
                                                @error('variation_price.'.$data->id.'.price') <small class="text-danger">{{ $message }}</small>@enderror
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <x-submit-btn text="Save" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
