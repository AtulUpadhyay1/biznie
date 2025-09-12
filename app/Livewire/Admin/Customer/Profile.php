<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use App\Models\UserDetail;

class Profile extends Component
{
    public $data, $priority;
    public function mount($id)
    {
        $this->data = User::with(['getUserDetail'])->find($id);
        $this->priority = $this->data->getUserDetail ? $this->data->getUserDetail->priority : null;
    }

    public function render()
    {
        return view('admin.customer_list.profile', ['page_title' => 'Buyer Profile']);
    }

    public function updatePriority($value)
    {
        $user_detail = UserDetail::where('user_id', $this->data->id)->first();
        if (!$user_detail) {
            $user_detail = new UserDetail();
            $user_detail->user_id = $this->data->id;
        }
        $user_detail->priority = $value;
        $user_detail->save();

        $this->dispatch('alert',
            type : 'success',
            message : 'Priority Updated Successfully.',
        );
    }
}
