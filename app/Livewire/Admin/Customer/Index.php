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
        $query = User::where('type', 'customer');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $list = $query->latest()->paginate(getPaginate());

        return view('admin.customer_list.index', compact('list'), ['page_title' => 'Customer List']);
    }
}
