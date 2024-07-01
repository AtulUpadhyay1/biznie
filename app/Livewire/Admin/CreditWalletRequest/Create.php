<?php

namespace App\Livewire\Admin\CreditWalletRequest;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CreditWalletRequest;
use App\Models\CreditWalletTransaction;

class Create extends Component
{
    public $page_title = "Add Credit Wallet Request";

    use WithFileUploads;

    public $user_id, $document = [], $reference_number, $status = 'Pending', $notes, $description, $amount = 0;

    public function render()
    {
        $user_list = User::where('status', 'active')->orderBy('name', 'asc')->get();
        return view('admin.credit_wallet_request.form', compact('user_list'));
    }

    public function save()
    {
        $this->validate([
            'user_id'               => 'required',
            'reference_number'      => 'required',
            'amount'                => 'nullable|min:1'
        ]);

        $check = CreditWalletRequest::where('user_id', $this->user_id)->where('status', 'Approved')->first();
        if($check){
            $this->dispatch('alert',
                type : 'error',
                message : 'This user already approved this request.',
            );
            return ;
        }
        $credit_wallet_request = new CreditWalletRequest;
        $credit_wallet_request->user_id = $this->user_id;
        $documents = [];
        foreach($this->document as $file){
            $documents[] = imageUpload($file, 'credit_wallet_request');
        }
        $credit_wallet_request->document = $documents;
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
            $history->transaction_status= 'Credit added by admin';
            $history->save();

            $history->transaction_id    = 'TX-'.date('Ymd').$history->id.$this->user_id.rand(111, 999);
            $history->save();
        }

        session()->flash('message', 'Credit Request has been added successfully.');
        return $this->redirectRoute('admin.credit-wallet-request.index',navigate: true);
    }
}
