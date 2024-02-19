<?php

namespace App\Livewire\Admin\Testimonial;

use Livewire\Component;
use App\Models\Testimonial;
use Livewire\WithPagination;

class Index extends Component
{
    public $page_title = 'Testimonial List';

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $list = Testimonial::latest()->paginate(getPaginate());
        return view('admin.testimonial.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try {

            $data = Testimonial::findOrFail($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Testimonial status updated successfully.',
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
        try{
            Testimonial::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Data deleted successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }

    }
}
