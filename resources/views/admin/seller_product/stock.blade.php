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
                                    @foreach ($list as $data)
                                        <tr>
                                            <th>
                                                {{ $loop->iteration }}
                                                @if($data->is_selected)
                                                    <i class="bi bi-check2-circle text-success fs-5"></i>
                                                @endif
                                            </th>
                                            @foreach ($data->value as $value)
                                                <td> {{ $value['value'] }} </td>
                                            @endforeach
                                            <td>
                                                <input type="number" class="form-control form-control-sm @error('variation_stock.'.$data->id.'.stock') is-invalid @enderror" wire:model="variation_stock.{{$data->id}}.stock">
                                                @error('variation_stock.'.$data->id.'.stock') <small class="text-danger">{{ $message }}</small>@enderror
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
