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

    /**
     * The two price grants, held separately: F.O.R and F.O.B are different
     * commercial terms, so a seller can be trusted with one and not the other.
     * Both off by default.
     */
    public $forPriceAccess = false;
    public $fobPriceAccess = false;

    public function mount($id)
    {
        $this->data = User::where('type', 'seller')->with('getSellerKycDetail', 'getBusiness')->findOrFail($id);
        $this->status = $this->data->getSellerKycDetail->status;
        $this->user_status = $this->data->status;
        $this->forPriceAccess = (bool) $this->data->for_price_access;
        $this->fobPriceAccess = (bool) $this->data->fob_price_access;
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

    /**
     * Grant or revoke the seller's F.O.R price panel.
     *
     * A seller-quoted F.O.R price overrides the calculated one for every buyer
     * in that city, so the panel stays hidden until admin turns this on. The
     * API enforces the same flag — hiding the card is not the control.
     */
    public function updateForPriceAccess()
    {
        $this->data->for_price_access = (bool) $this->forPriceAccess;
        $this->data->save();

        session()->flash('success', $this->forPriceAccess
            ? 'F.O.R price access enabled for this seller.'
            : 'F.O.R price access disabled for this seller.');

        return $this->redirect('/admin/seller-kyc-detail/'.$this->data->id, navigate: true);
    }

    /**
     * Grant or revoke the seller's F.O.B price panel.
     *
     * The panel itself does not exist yet; the grant is stored now so the card
     * can be mapped onto it when it is built.
     */
    public function updateFobPriceAccess()
    {
        $this->data->fob_price_access = (bool) $this->fobPriceAccess;
        $this->data->save();

        session()->flash('success', $this->fobPriceAccess
            ? 'F.O.B price access enabled for this seller.'
            : 'F.O.B price access disabled for this seller.');

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
