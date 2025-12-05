<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class Profile extends Component
{
    public $data, $priority;
    public $user_status;
    public $showBlockModal = false;
    public $blockReason = '';
    public function mount($id)
    {
        $this->data = User::with(['getUserDetail'])->find($id);
        $this->priority = $this->data->getUserDetail ? $this->data->getUserDetail->priority : null;
        $this->user_status = $this->data->status ?? null;
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

    public function openBlockModal()
    {
        $this->blockReason = '';
        $this->showBlockModal = true;
        $this->dispatch('open-block-modal');
    }

    public function confirmBlock()
    {
        $this->validate([
            'blockReason' => 'required|string|max:1000',
        ]);

        $this->data->status = 'in_active';

        $this->data->block_reason = $this->blockReason;
        $this->data->save();

        $this->showBlockModal = false;
        $this->dispatch('close-block-modal');

        session()->flash('success', 'Customer account blocked successfully.');
        return $this->redirect(url()->current(), navigate: true);
    }

    public function unblock()
    {
        $this->data->status = 'active';
        $this->data->save();
        Log::info('Customer unblocked', ['user_id' => $this->data->id, 'by' => auth()->id()]);
        session()->flash('success', 'Customer account unblocked successfully.');
        return $this->redirect(url()->current(), navigate: true);
    }
}
