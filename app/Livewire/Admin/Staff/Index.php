<?php

namespace App\Livewire\Admin\Staff;

use App\Models\Admin;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

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
        $this->authorize('staff-list');
    }

    public function render()
    {
        $list = Admin::where('id', '!=', 1)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->simplePaginate(getPaginate());
        return view('admin.staff.index', compact('list'), ['page_title' => 'Staff List']);
    }

    public function delete($id)
    {
        try {
            $admin = Admin::findOrFail($id);
            if ($admin->id == 1) {
                $this->dispatch('alert',
                    type: 'error',
                    message: 'You cannot delete the super admin.'
                );
                return;
            }
            $admin->delete();
            DB::table('model_has_roles')->where('model_id',$id)->delete();
            $this->dispatch('alert',
                type: 'success',
                message: 'Staff deleted successfully.'
            );
        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong, please try again.'
            );
        }
    }
}
