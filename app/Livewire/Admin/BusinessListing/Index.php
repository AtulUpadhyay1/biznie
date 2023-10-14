<?php

namespace App\Livewire\Admin\BusinessListing;

use Livewire\Component;
use App\Models\Business;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $list = Business::with('getUser', 'getSellerKycDetail')->latest()->paginate(getPaginate());
        return view('admin.business_listing.index', compact('list'), ['page_title' => 'Business Listing']);
    }
}
