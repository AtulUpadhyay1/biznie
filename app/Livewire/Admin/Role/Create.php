<?php

namespace App\Livewire\Admin\Role;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Create extends Component
{
    public $hidden_id, $name, $permission = [];
    public $search = null;
    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function render()
    {
        $permissions = Permission::query()
            ->when($this->search, function ($query) {
                $query->where('parent_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('parent_name', 'asc')
            ->get()
            ->groupBy('parent_name');
        return view('admin.role.form', compact('permissions'), ['page_title' => 'Create Role']);
    }

    public function save()
    {
        $this->validate([
            'name'       => 'required|string|max:255|unique:roles,name',
            'permission' => 'required|array|min:1',
        ]);

        try {
            $role = new Role;
            $role->name        = $this->name;
            $role->guard_name  = 'admin';
            $role->save();
            $role->syncPermissions(array_map('intval', $this->permission));

            session()->flash('message', 'Role created successfully.');
            return $this->redirectRoute('admin.role.index', navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong, please try again.'
            );
        }

    }
}
