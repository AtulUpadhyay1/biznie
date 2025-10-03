<?php

namespace App\Livewire\Admin\Role;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Edit extends Component
{
    public $hidden_id, $name, $permission = [];
    public $search = null;
    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function mount($id)
    {
        $this->authorize('role-edit');
        $this->hidden_id = $id;
        $role = Role::findById($this->hidden_id, 'admin');
        $this->name = $role->name;
        $this->permission = $role->permissions->pluck('id')->toArray();
    }

    public function render()
    {
        $permissions = Permission::query()
            ->when($this->search, function ($query) {
                $query->where('parent_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('parent_name', 'asc')
            ->get()
            ->groupBy('parent_name');

        return view('admin.role.form', compact('permissions'), ['page_title' => 'Edit Role']);
    }

    public function update()
    {
        $this->validate([
            'name'       => 'required|string|max:255|unique:roles,name,' . $this->hidden_id,
            'permission' => 'required|array|min:1',
        ]);

        try {
            $role = Role::findById($this->hidden_id, 'admin');
            $role->name        = $this->name;
            $role->guard_name  = 'admin';
            $role->save();
            $role->syncPermissions(array_map('intval', $this->permission));

            session()->flash('message', 'Role updated successfully.');
            return $this->redirectRoute('admin.role.index', navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong, please try again.'
            );
        }

    }
}
