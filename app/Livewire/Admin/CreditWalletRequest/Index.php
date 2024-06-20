<?php

namespace App\Livewire\Admin\CreditWalletRequest;

use Livewire\Component;
use App\Models\CreditWalletRequest;

class Index extends Component
{
    public $page_title = "Credit Wallet Request";

    public function render()
    {
        $list = CreditWalletRequest::latest()->get();
        return view('admin.credit_wallet_request.index');
    }
}
