<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search, $status;
    protected $queryString = [
        'search'        => ['except' => ''],
        'status'        => ['except' => ''],
    ];

    public function render()
    {
        $query = User::search($this->search)->where('type', 'customer');
        if ($this->status) {
            $query->where('status', $this->status);
        }

        $list = $query->latest()->paginate(getPaginate());

        return view('admin.customer_list.index', compact('list'), ['page_title' => 'Buyers List']);
    }
}
