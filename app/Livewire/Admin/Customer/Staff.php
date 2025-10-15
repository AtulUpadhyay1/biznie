<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;

class Staff extends Component
{
    public $data, $priority;
    public function mount($id)
    {
        $this->data = User::with(['getUserDetail'])->find($id);
        $this->priority = $this->data->getUserDetail ? $this->data->getUserDetail->priority : null;
    }

    public function render()
    {
        $list = User::where('added_by', $this->data->id)
            ->where('is_staff', '1')
            ->latest()
            ->simplePaginate(getPaginate(25));
        return view('admin.customer_list.staff', compact('list'), ['page_title' => 'Staff List']);
    }
}
