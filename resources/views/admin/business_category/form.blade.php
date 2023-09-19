<div>
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add Business Categories</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <a class="btn btn-danger btn-icon-text float-end align-items-center" wire:click="cancel()">
                                <i class="bi bi-x-lg me-1"></i>Cancel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="forms-sample" wire:submit.prevent="{{$hidden_id ? 'update()' : 'save()'}}">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Name:</label>
                                <input type="text" class="form-control mb-4 mb-md-0" wire:model.defer="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Choose Image:</label>
                                <input id="business-category-image" type="file" class="form-control mb-2" wire:model.defer="image">
                               <div for="business-category-image" class="lable-image justify-content-center">
                                    @if($image)
                                        <img src="{{$image->temporaryUrl()}}" class="label-image-url">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-warning">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
