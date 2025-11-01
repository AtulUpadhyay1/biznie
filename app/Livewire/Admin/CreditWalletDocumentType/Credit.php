<?php

namespace App\Livewire\Admin\CreditWalletDocumentType;

use Livewire\Component;
use App\Models\CreditWalletDocumentType;

class Credit extends Component
{
    public $page_title = 'Create Credit Wallet Document Type';

    public $hidden_id, $name, $title=[], $description=[];
    public $input_count = 0, $inputs = [];
    public $fields = [];

    public function render()
    {
        return view('admin.credit_wallet_document_type.form');
    }

    public function addField()
    {
        $this->fields[] = [
            'label'         => '',
            'required'      => false,
            'type'          => 'text',
            'description'   => ''
        ];
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function save()
    {
        $this->validate([
            'name'          => 'required',
        ],[
            'name.required'          => 'Document name is required',
        ]);

        foreach ($this->fields as $field) {
            $this->validate([
                'fields.*.label'         => 'required',
                'fields.*.type'          => 'required',
                'fields.*.required'      => 'boolean',
            ],[
                'fields.*.label.required'        => 'Field label is required',
                'fields.*.type.required'         => 'Field type is required',
            ]);
        }

        $data = new CreditWalletDocumentType;
        $data->name         = $this->name;
        $data->forms        = $this->fields;
        $data->save();

        session()->flash('success', 'Credit wallet document added successfully !!');
        return $this->redirectRoute('admin.credit-wallet-document-type.index', navigate: true);
    }
}
