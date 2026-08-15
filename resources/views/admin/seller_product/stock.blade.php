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
                                        <th>#</th>
                                        @php
                                            $attributes = $list[0]->value;
                                        @endphp
                                        @foreach ($attributes as $attribute)
                                            <th>
                                                {{$attribute['name']}}
                                            </th>
                                        @endforeach
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($list as $data)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                                @if($data->is_selected)
                                                    <i class="bi bi-check2-circle text-success"></i>
                                                @endif
                                            </td>
                                            @foreach ($data->value as $value)
                                                <td> {{ $value['value'] }} </td>
                                            @endforeach
                                            <td>
                                                <label class="form-label visually-hidden" for="variation_stock_{{$data->id}}">Stock</label>
                                                <input type="number" id="variation_stock_{{$data->id}}" class="form-control form-control-sm @error('variation_stock.'.$data->id.'.stock') is-invalid @enderror" wire:model="variation_stock.{{$data->id}}.stock">
                                                @error('variation_stock.'.$data->id.'.stock') <small class="text-danger">{{ $message }}</small>@enderror
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
