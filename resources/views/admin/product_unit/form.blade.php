<div>
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add Unit</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-icon-text float-end align-items-center" wire:click="cancel()">
                                <i class="bi bi-x-lg me-1"></i>Cancel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="forms-sample" wire:submit.prevent="{{$hidden_id ? 'update()' : 'save()'}}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Name:</label>
                                <input type="text" class="form-control mb-4 mb-md-0" wire:model.defer="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Unit:</label>
                                <input type="text" class="form-control mb-4 mb-md-0" wire:model.defer="unit">
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
