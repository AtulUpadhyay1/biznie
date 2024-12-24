<?php

namespace App\Livewire\Admin\Transporter;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TransporterProductEnquiry;

class Enquiry extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $page_title = 'Transporter Enquiry';

    public $data;

    public function mount($id)
    {
        $this->data = User::findOrFail($id);
    }

    public function render()
    {
        $list = TransporterProductEnquiry::where('user_id', $this->data->id)->latest()->simplePaginate(getPaginate(25));
        return view('admin.transporter.enquiry', compact('list'));
    }
}
