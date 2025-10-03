<?php

namespace App\Livewire\Admin\Seller;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    protected $queryString = [
        'search'        => ['except' => '']
    ];

    public function mount()
    {
        $this->authorize('seller-list');
    }

    public function render()
    {
        $list = User::search($this->search)
            ->where('type', 'seller')
            ->with('getBusiness', 'getSellerKycDetail', 'getSellerProductEnquiries', 'getSellerOrders', 'getUserDetail')
            ->latest()
            ->where('is_staff', 0)
            ->simplePaginate(getPaginate());
        return view('admin.seller.index', compact('list'), ['page_title' => 'Seller List']);
    }

}
