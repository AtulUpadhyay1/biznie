<?php

namespace App\Livewire\Admin\Transporter;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Transporter List';
    public $show;

    public function render()
    {
        $list = User::where('type', 'transporter')->latest()->paginate(getPaginate());
        return view('admin.transporter.index', compact('list'));
    }

    public function showDetail($id)
    {
        $this->show = User::with('getTransporterDetail')->findOrFail($id);
    }
}
