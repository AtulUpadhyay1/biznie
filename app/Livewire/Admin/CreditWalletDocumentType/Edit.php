<?php

namespace App\Livewire\Admin\CreditWalletDocumentType;

use Livewire\Component;
use App\Models\CreditWalletDocumentType;

class Edit extends Component
{
    public $page_title = 'Edit Credit Wallet Document Type';

    public $hidden_id, $name, $title=[], $description=[];
    public $input_count = 0, $inputs = [];

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = CreditWalletDocumentType::findOrFail($this->hidden_id);

        $this->name = $data->name;

        $this->title = $data->title;
        $this->description = $data->description;

        $this->input_count = count($data->title);

        foreach(array_slice($data->title, 1) as $key => $title){
            $this->addField($key);
        }
    }

    public function render()
    {
        return view('admin.credit_wallet_document_type.form');
    }

    public function addField($input_count)
    {
        $input_count = $input_count + 1;
        $this->input_count = $input_count;
        array_push($this->inputs, $input_count);
    }

    public function removeField($input_count)
    {
        unset($this->inputs[$input_count]);

        unset($this->title[$input_count+1]);
        unset($this->description[$input_count+1]);
    }

    public function update()
    {
        $this->validate([
            'name'          => 'required',
            'title.0'       => 'required',
            'description.0' => 'required',
        ],[
            'name.required'          => 'Document name is required',
            'title.*.required'       => 'Title is required',
            'description.*.required' => 'Description is required',
        ]);

        foreach ($this->inputs as $value) {
            $this->validate([
                'title.'.$value         => 'required',
                'description.'.$value   => 'required',
            ],[
                'title.'.$value.'.required'       => 'Title is required',
                'description.'.$value.'.required' => 'Description is required',
            ]);
        }

        $data = CreditWalletDocumentType::findOrFail($this->hidden_id);
        $data->name         = $this->name;
        $data->title        = $this->title;
        $data->description  = $this->description;
        $data->save();

        session()->flash('success', 'Credit wallet document updated successfully !!');
        return $this->redirectRoute('admin.credit-wallet-document-type.index', navigate: true);
    }
}
