<?php

namespace App\Livewire\Admin\Banner;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    public $page_title = 'Banner List';

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    protected $queryString = [
        'search'        => ['except' => '']
    ];

    public function render()
    {
        $list = Banner::search($this->search)->orderBy('created_at','desc')->paginate(getPaginate());
        return view('admin.banner.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try {

            $data = Banner::findOrFail($id);
            $data->published = $data->published ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Banner status updated successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong.',
            );
        }

    }

    public function delete($id)
    {
        try {

            Banner::destroy($id);

            $this->dispatch('alert',
                type : 'success',
                message : 'Banner status deleted successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong.',
            );
        }

    }
}
