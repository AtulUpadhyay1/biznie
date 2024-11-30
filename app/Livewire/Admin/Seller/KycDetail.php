<?php

namespace App\Livewire\Admin\Seller;

use App\Models\User;
use Livewire\Component;

class KycDetail extends Component
{
    public $data, $status;
    public function mount($id)
    {
        $this->data = User::where('type', 'seller')->with('getSellerKycDetail', 'getBusiness')->findOrFail($id);
        $this->status = $this->data->getSellerKycDetail->status;
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
}
