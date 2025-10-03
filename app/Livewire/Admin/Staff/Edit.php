<?php

namespace App\Livewire\Admin\Staff;

use App\Models\Admin;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    public $page_title = 'Edit Staff';
    public $hidden_id, $name, $email, $phone, $password, $roles;
    public $search = null;

    public function mount($id)
    {
        $this->authorize('staff-edit');
        $this->hidden_id = $id;
        $staff = Admin::findOrFail($this->hidden_id);
        $this->name = $staff->name;
        $this->email = $staff->email;
        $this->phone = $staff->phone;
        $this->roles = $staff->getRoleNames()->toArray();
    }

    public function render()
    {
        $roles_list = Role::where('id', '!=', 1)
            ->orderBy('name','asc')
            ->pluck('name','name')
            ->all();
        return view('admin.staff.form', compact('roles_list'), ['page_title' => $this->page_title]);
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins,email,'.$this->hidden_id,
            'phone' => 'required|string|unique:admins,phone,'.$this->hidden_id,
            'password' => 'nullable|string|min:8',
            'roles' => 'required',
        ]);

        try {

            $admin = Admin::findOrFail($this->hidden_id);
            $admin->name = $this->name;
            $admin->email = $this->email;
            $admin->phone = $this->phone;
            if ($this->password) {
                $admin->password = bcrypt($this->password);
            }
            $admin->save();

            $admin->syncRoles($this->roles);

            session()->flash('success', 'Staff updated successfully!');
            return redirect()->route('admin.staff.index');

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong, please try again.'
            );
        }
    }
}
