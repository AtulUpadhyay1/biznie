<?php

namespace App\Livewire\Admin\Transporter;

use App\Models\User;
use Livewire\Component;
use App\Models\TransporterDetail;

class Profile extends Component
{
    public $page_title = 'Transporter Profile';

    public function mount($id)
    {
        $this->hidden_id      = $id;
    }

    public function render()
    {
        $data = User::with('getTransporterDetail')->findOrFail($this->hidden_id);
        return view('admin.transporter.profile', compact('data'));
    }
}
