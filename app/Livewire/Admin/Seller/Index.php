<?php

namespace App\Livewire\Admin\Seller;

use Livewire\Component;

class Index extends Component
{

    public function render()
    {
        return view('admin.seller_list.index', ['page_title' => 'Seller List']);
    }

}
