<?php

namespace App\Livewire\Admin\Seller;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class KycDetail extends Component
{
    public $data, $status;
    public $user_status;
    public $showBlockModal = false;
    public $blockReason = '';

    public function mount($id)
    {
        $this->data = User::where('type', 'seller')->with('getSellerKycDetail', 'getBusiness')->findOrFail($id);
        $this->status = $this->data->getSellerKycDetail->status;
        $this->user_status = $this->data->status;
    }

    public function render()
    {
        return view('admin.seller.kyc_detail', ['page_title' => 'Seller Kyc Detail']);
    }

    public function updateStatus()
    {
        $kycDetail = $this->data->getSellerKycDetail;
        $kycDetail->status = $this->status;
        $kycDetail->save();
        session()->flash('success', 'Kyc Detail updated successfully !!');
        return $this->redirect('/admin/seller-kyc-detail/'.$this->data->id, navigate: true);
    }

    // Open the block modal (client will show modal via browser event)
    public function openBlockModal()
    {
        $this->blockReason = '';
        $this->showBlockModal = true;
        $this->dispatch('open-block-modal');
    }

    // Confirm block with reason
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

        session()->flash('success', 'Seller account blocked successfully.');
        return $this->redirect('/admin/seller-kyc-detail/'.$this->data->id, navigate: true);
    }

    // Unblock the user
    public function unblock()
    {
        $this->data->status = 'active';
        $this->data->save();
        session()->flash('success', 'Seller account unblocked successfully.');
        return $this->redirect('/admin/seller-kyc-detail/'.$this->data->id, navigate: true);
    }
}
