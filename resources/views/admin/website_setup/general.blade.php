<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                </div>
                <form wire:submit.prevent="update()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="phone1">Phone 1</label>
                                    <input type="text" class="form-control" id="phone1" wire:model="value.phone1" placeholder="Enter Phone 1">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="phone2">Phone 2</label>
                                    <input type="text" class="form-control" id="phone2" wire:model="value.phone2" placeholder="Enter Phone 2">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="whatsapp">WhatsApp</label>
                                    <input type="text" class="form-control" id="whatsapp" wire:model="value.whatsapp" placeholder="Enter WhatsApp">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" id="email" wire:model="value.email" placeholder="Enter Email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="about">Short About</label>
                                    <textarea class="form-control" id="about" wire:model="value.short_about" placeholder="Enter About"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="address">Address</label>
                                    <textarea class="form-control" id="address" wire:model="value.address" placeholder="Enter Address"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="facebook">Facebook</label>
                                    <input type="text" class="form-control" id="facebook" wire:model="value.facebook" placeholder="Enter Facebook URL">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="twitter">Twitter</label>
                                    <input type="text" class="form-control" id="twitter" wire:model="value.twitter" placeholder="Enter Twitter URL">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="instagram">Instagram</label>
                                    <input type="text" class="form-control" id="instagram" wire:model="value.instagram" placeholder="Enter Instagram URL">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="youtube">YouTube</label>
                                    <input type="text" class="form-control" id="youtube" wire:model="value.youtube" placeholder="Enter YouTube URL">
                                </div>
                            </div>
                        </div>
                        <x-submit-btn text="Update Settings" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
