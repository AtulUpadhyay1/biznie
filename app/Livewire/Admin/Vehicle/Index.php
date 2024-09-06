<?php

namespace App\Livewire\Admin\Vehicle;

use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Vehicle List';

    public function render()
    {
        $list = Vehicle::latest()->paginate(getPaginate());
        return view('admin.vehicle.index', compact('list'));
    }
}
