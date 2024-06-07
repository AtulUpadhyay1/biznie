<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Seller Tag & Priority</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.seller-list') }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save()">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tags</label>
                                <div>
                                    @foreach ($seller_tag as $tag)
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" name="tag" class="form-check-input" value="{{$tag->id}}" wire:model="tag" id="tag_{{$tag->id}}">
                                            <label class="form-check-label" for="tag_{{$tag->id}}">
                                                {{$tag->name}}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="priority">Priority</label>
                                <select class="form-select" name="priority" id="priority" wire:model="priority">
                                    <option value="">Select priority</option>
                                    @for ($i=1; $i<=5; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="Update" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
