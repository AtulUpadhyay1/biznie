<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Seller Tag & Priority</h4>
                    <div class="bz-toolbar">
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.seller.index') }}" wire:navigate>
                            <i class="bi bi-x-lg"></i>Cancel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save()">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tags</label>
                                <div>
                                    @forelse ($seller_tag as $tag)
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" name="tag" class="form-check-input" value="{{$tag->id}}" wire:model="tag" id="tag_{{$tag->id}}">
                                            <label class="form-check-label" for="tag_{{$tag->id}}">
                                                {{$tag->name}}
                                            </label>
                                        </div>
                                    @empty
                                        <div class="alert alert-danger p-2" role="alert">
                                            No tag added yet! Please <a href="{{ route('admin.seller-tag.create') }}" wire:navigate class="alert-link">click here</a> to add tag.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
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
