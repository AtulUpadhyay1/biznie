<?php

namespace App\Livewire\Admin\Seller;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $list = User::where('type', 'seller')->with('getBusiness', 'getSellerKycDetail')->latest()->paginate(getPaginate());
        return view('admin.seller.index', compact('list'), ['page_title' => 'Seller List']);
    }

}
