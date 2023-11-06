<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Product List';
    public $status;
    protected $queryString = [
        'status'        => ['except' => '']
    ];

    public function render()
    {
        $list = Product::latest()->with('getBusiness');
        if($this->status == 'pending'){

            $this->page_title = 'New Product Request List';

            $list = $list->where('request_status', 'pending')->paginate(getPaginate());
            return view('admin.product.pending_list', compact('list'));

        }elseif($this->status == 'rejected'){

            $this->page_title = 'Rejected Product List';

            $list = $list->where('request_status', 'rejected')->paginate(getPaginate());
            return view('admin.product.rejected_list', compact('list'));

        }else{

            $list = $list->where('request_status', 'approved')->paginate(getPaginate());
            return view('admin.product.index', compact('list'));
        }

    }

    public function updateStatus($id)
    {
        try {

            $data = Product::findOrFail($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Product status updated successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }

    }

    public function updateFeatureStatus($id)
    {
        try {

            $data = Product::findOrFail($id);
            $data->featured_status = $data->featured_status ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Product featured status updated successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }

    }

    public function updateRequestStatus($id, $request_status)
    {
        try {

            $data = Product::findOrFail($id);
            $data->request_status = $request_status;
            $data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Product '.$request_status.' updated successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }
    }
}
