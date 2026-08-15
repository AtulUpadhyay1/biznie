<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.transporter.index') }}" wire:navigate>
                            <i class="bi bi-arrow-left"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="assignVehicle()">
                        <div class="row">
                            @foreach ($vehicle_list as $vehicle_data)
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <input type="checkbox" class="form-check-input" id="vehicle_{{$vehicle_data->id}}" wire:model="vehicle" value="{{$vehicle_data->id}}" style="position: absolute;">
                                        <img src="{{ imageUrl($vehicle_data->photo) }}" class="card-img-top">
                                        <div class="card-body">
                                            <p><b>Name : </b>{{ $vehicle_data->name }}</p>
                                            <p><b>Type : </b>{{ $vehicle_data->type }}</p>
                                            <p><b>Capacity : </b>{{ $vehicle_data->capacity }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="Assign Vehicle" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
