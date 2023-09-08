<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class DashboardLivewire extends Component
{
    public function render()
    {
        return view('admin.dashboard', ['page_title' => 'Admin Dashboard']);
    }
}
