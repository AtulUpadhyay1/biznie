<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save()">
                        <div class="row mb-3">

                            <div class="col-md-12 mb-3" wire:ignore>
                                <textarea id="value" class="form-control @error('value') is-invalid @enderror" wire:model="value" rows="10" placeholder="Enter About Us"></textarea>
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
