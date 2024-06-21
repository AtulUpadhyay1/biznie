<?php

namespace App\Livewire\Admin\CreditWalletRequest;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CreditWalletRequest;
use App\Models\CreditWalletTransaction;

class Show extends Component
{
    public $page_title = "Show";

    use WithFileUploads;

    public $hidden_id, $data, $user_id, $document, $reference_number, $status = 'Pending', $notes, $description, $amount = 0;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $this->data = CreditWalletRequest::with('getUser')->findOrFail($this->hidden_id);
        $this->user_id = $this->data->user_id;
        $this->reference_number = $this->data->reference_number;
        $this->status = $this->data->status;
        $this->notes = $this->data->notes;
        $this->description = $this->data->description;
        $this->amount = $this->data->getUser->assign_credit_balance;
    }

    public function render()
    {
        return view('admin.credit_wallet_request.show');
    }

    public function save()
    {
        $this->validate([
            'document'              => 'nullable|file',
            'reference_number'      => 'required',
            'amount'                => 'nullable|min:1'
        ]);

        // $check = CreditWalletRequest::where('user_id', $this->user_id)->where('status', 'Approved')->first();
        // if($check){
        //     $this->dispatch('alert',
        //         type : 'error',
        //         message : 'This user already approved this request.',
        //     );
        //     return ;
        // }

        $credit_wallet_request = $this->data;
        $credit_wallet_request->user_id = $this->user_id;
        $credit_wallet_request->document = $this->document ? imageUpload($this->document, 'credit_wallet_request') : $credit_wallet_request->document;
        $credit_wallet_request->reference_number = $this->reference_number;
        $credit_wallet_request->status = $this->status;
        $credit_wallet_request->notes = $this->notes;
        $credit_wallet_request->description = $this->description;
        $credit_wallet_request->save();

        if($this->status == 'Approved'){

            $user = User::find($this->user_id);
            $user->credit_balance = $user->credit_balance + $this->amount;
            $user->assign_credit_balance = $this->amount;
            $user->save();

            $history = new CreditWalletTransaction;
            $history->user_id           = $this->user_id;
            $history->amount            = $this->amount;
            $history->description       = $this->description;
            $history->notes             = $this->notes;
            $history->status            = 'credit';
            $history->transaction_status= 'Credit updated by admin';
            $history->save();

            $history->transaction_id    = 'TX-'.date('Ymd').$history->id.$this->user_id.rand(111, 999);
            $history->save();
        }

        session()->flash('message', 'Credit Request has been updated successfully.');
        return $this->redirectRoute('admin.credit-wallet-request.index',navigate: true);
    }
}
