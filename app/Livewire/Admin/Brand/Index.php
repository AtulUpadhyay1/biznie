<?php

namespace App\Livewire\Admin\Brand;

use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search, $status;
    protected $queryString = [
        'search'        => ['except' => '']
    ];

    public function render()
    {
        $list = Brand::search($this->search)->latest()->paginate(getPaginate());
        return view('admin.brand.index', compact('list'), ['page_title' => 'Brand']);
    }

    public function updateStatus($id)
    {
        try{
            $data = Brand::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Brand active successfully !!': 'Brand inactive successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function updateFeatured($id)
    {
        try{
            $data = Brand::find($id);
            $data->featured = $data->featured ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->featured==1 ? 'success' : 'error',
                message: $data->featured== 1 ? 'Brand featured successfully !!': 'Brand unfeatured successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function delete($id)
    {
        try{
            Brand::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Brand deleted successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
