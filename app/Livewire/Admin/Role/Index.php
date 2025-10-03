<?php

namespace App\Livewire\Admin\Role;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

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
        $this->authorize('role-list');
    }

    public function render()
    {
        $list = Role::where('id', '!=', 1)
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->simplePaginate(getPaginate());
        return view('admin.role.index', compact('list'), ['page_title' => 'Role List']);
    }

    public function delete($id)
    {
        try {
            $role = Role::findById($id, 'admin');
            $role->delete();

            session()->flash('message', 'Role deleted successfully.');
            return $this->redirectRoute('admin.role.index', navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong, please try again.'
            );
        }
    }
}
