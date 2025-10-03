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

    public $search, $status;
    protected $queryString = [
        'search'        => ['except' => ''],
        'status'        => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('transporter-list');
    }

    public function render()
    {
        $query = User::search($this->search)->where('type', 'transporter');
        if ($this->status) {
            $query->where('status', $this->status);
        }
        $list = $query->latest()->paginate(getPaginate());
        return view('admin.transporter.index', compact('list'));
    }
}
