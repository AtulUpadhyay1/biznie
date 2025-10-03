<?php

namespace App\Livewire\Admin\Staff;

use App\Models\Admin;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    public $hidden_id, $name, $email, $phone, $password, $roles;
    public $search = null;
    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function mount()
    {
        $this->authorize('staff-create');
    }

    public function render()
    {
        $roles_list = Role::where('id', '!=', 1)
            ->orderBy('name','asc')
            ->pluck('name','name')
            ->all();
        return view('admin.staff.form', compact('roles_list'), ['page_title' => 'Create Staff']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins,email',
            'phone' => 'required|string|unique:admins,phone',
            'password' => 'required|string|min:8',
            'roles' => 'required',
        ]);

        try {

            $admin = new Admin();
            $admin->name = $this->name;
            $admin->email = $this->email;
            $admin->phone = $this->phone;
            $admin->password = bcrypt($this->password);
            $admin->save();

            $admin->assignRole($this->roles);

            session()->flash('success', 'Staff created successfully!');
            return redirect()->route('admin.staff.index');

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong, please try again.'
            );
        }
    }
}
