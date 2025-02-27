<?php

namespace App\Livewire\Admin\IngotPrice;

use Livewire\Component;
use App\Models\IngotPrice;

class Index extends Component
{
    public function render()
    {
        $list = IngotPrice::latest()->paginate(getPaginate());
        return view('admin.ingot_price.index', compact('list'), ['page_title' => 'Ingot Price']);
    }
}
