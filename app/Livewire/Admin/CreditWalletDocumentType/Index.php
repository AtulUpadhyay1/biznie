<?php

namespace App\Livewire\Admin\CreditWalletDocumentType;

use Livewire\Component;
use App\Models\CreditWalletDocumentType;

class Index extends Component
{
    public $page_title = "Credit Wallet Document Type";

    public function render()
    {
        $list = CreditWalletDocumentType::latest()->get();
        return view('admin.credit_wallet_document_type.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try{
            $data = CreditWalletDocumentType::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Status active successfully !!': 'Status inactive successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function delete($id)
    {
        try{
            CreditWalletDocumentType::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Data deleted successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
