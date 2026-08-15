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
                            <div class="col-md-12 mb-3" wire:ignore>
                                <label class="form-label" for="value">{{ $page_title }}</label>
                                <textarea id="value" class="form-control @error('value') is-invalid @enderror" wire:model="value" rows="10" placeholder="Enter {{ $page_title }}"></textarea>
                                @error('value')
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
                const value = CKEDITOR.replace('value');
                value.on('change', function(event){
                    @this.set('value', event.editor.getData());
                });
            });
        </script>
    @endpush
</div>
