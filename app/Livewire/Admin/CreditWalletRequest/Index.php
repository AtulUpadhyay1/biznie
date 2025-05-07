<?php

namespace App\Livewire\Admin\CreditWalletRequest;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CreditWalletRequest;

class Index extends Component
{
    public $page_title = "Credit Wallet Request";

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $list = CreditWalletRequest::with('getUser', 'getUser.getUserDetail')->latest()->paginate(getPaginate());
        return view('admin.credit_wallet_request.index', compact('list'));
    }
}
