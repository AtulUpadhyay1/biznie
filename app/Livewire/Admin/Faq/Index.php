<?php

namespace App\Livewire\Admin\Faq;

use App\Models\Faq;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Faq List';

    public function render()
    {
        $list = Faq::latest()->paginate(getPaginate());
        return view('admin.faq.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try{
            $data = Faq::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'FAQ active successfully !!': 'FAQ inactive successfully !!'
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
            Faq::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'FAQ deleted successfully !!'
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
