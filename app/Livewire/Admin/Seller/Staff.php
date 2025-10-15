<?php

namespace App\Livewire\Admin\Seller;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Staff extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $data, $status;
    public function mount($id)
    {
        $this->data = User::where('type', 'seller')->with('getSellerKycDetail', 'getBusiness')->findOrFail($id);
        $this->status = $this->data->getSellerKycDetail->status;
    }

    public function render()
    {
        $list = User::where('added_by', $this->data->id)
            ->where('is_staff', '1')
            ->latest()
            ->simplePaginate(getPaginate(25));
        return view('admin.seller.staff', compact('list'), ['page_title' => 'Seller Staff List']);
    }
}
