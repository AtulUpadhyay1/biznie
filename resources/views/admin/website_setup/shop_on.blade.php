<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save()">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" id="title" class="form-control @error('title') is-invalid @enderror" wire:model="title" placeholder="Enter Title">
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="video_link" class="form-label">Video Url</label>
                                <input type="url" id="video_link" class="form-control @error('video_link') is-invalid @enderror" wire:model="video_link" placeholder="Enter Sub Title">
                                @error('video_link')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3" wire:ignore>
                                <label class="form-label" for="description">Description</label>
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" wire:model="description" rows="10" placeholder="Enter Description"></textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="Save" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function () {
                const description = CKEDITOR.replace('description');
                description.on('change', function(event){
                    @this.set('description', event.editor.getData());
                });
            });
        </script>
    @endpush
</div>
