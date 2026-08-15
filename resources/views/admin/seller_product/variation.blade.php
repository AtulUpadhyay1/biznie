<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-secondary btn-sm" wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
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
                                    @forelse ($list as $data)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <label class="form-check-label" for="exampleCheck{{$loop->iteration}}">
                                                        {{ $loop->iteration }}
                                                    </label>
                                                    <input type="checkbox" class="form-check-input" id="exampleCheck{{$loop->iteration}}" wire:model="variation_price.{{$data->id}}.is_selected" value="1" @if($variation_price[$data->id]['is_selected']) checked @endif wire:click="checkSelectAll()">
                                                </div>
                                                {{-- @if($data->is_selected)
                                                    <i class="bi bi-check2-circle text-success"></i>
                                                @endif --}}
                                            </td>
                                            @foreach ($data->value as $price_value)
                                                <td> {{ $price_value['value'] }} </td>
                                            @endforeach
                                            <td>
                                                <label class="form-label visually-hidden" for="variation_price_{{$data->id}}">Guage Difference</label>
                                                <input type="number" id="variation_price_{{$data->id}}" class="form-control form-control-sm @error('variation_price.'.$data->id.'.price') is-invalid @enderror" wire:model="variation_price.{{$data->id}}.price">
                                                @error('variation_price.'.$data->id.'.price') <small class="text-danger">{{ $message }}</small>@enderror
                                            </td>
                                        </tr>
                                    @empty
                                        <x-table-no-data />
                                    @endforelse
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
