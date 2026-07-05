<?php

namespace App\Livewire\Admin\SellerRequest;

use App\Mail\SellerRequestStatusChangedMail;
use App\Models\Business;
use App\Models\SellerKycDetail;
use App\Models\SellerOnboardingDetail;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserPromotionHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Show extends Component
{
    public SellerOnboardingDetail $requestRecord;
    public string $reviewNote = '';

    public function mount(int $id): void
    {
        $this->authorize('seller-list');

        $request = SellerOnboardingDetail::with(['getUser', 'getReviewer'])->findOrFail($id);
        $this->requestRecord = $request;
        $this->reviewNote = $request->review_note ?? '';
    }

    public function approve(): void
    {
        $this->authorize('seller-list');
        $record = $this->requestRecord->fresh(['getUser', 'getReviewer']);

        if ($record->request_status === 'approved') {
            $this->dispatch('toast', type: 'error', message: 'This request is already approved.');
            return;
        }

        DB::transaction(function () use ($record) {
            $user = User::findOrFail($record->user_id);
            $oldType = $user->type ?? 'customer';

            $user->name = $record->contact_person ?: $user->name;
            if ($record->email) {
                $user->email = $record->email;
            }
            if ($record->mobile) {
                $user->phone = $record->mobile;
            }
            $user->type = 'seller';
            $user->save();

            $log = new UserPromotionHistory();
            $log->user_id = $user->id;
            $log->old_type = $oldType;
            $log->new_type = 'seller';
            $log->save();

            $business = Business::where('user_id', $user->id)->first() ?: new Business();
            $business->user_id = $user->id;
            $business->name = $record->company_name;
            $business->seller_type = $record->seller_type_id ? [$record->seller_type_id] : $business->seller_type;
            $business->type = $record->business_type ? [$record->business_type] : $business->type;
            $business->category = $record->category ? [$record->category] : $business->category;
            $business->about = $record->products;
            $business->save();

            $kyc = SellerKycDetail::where('user_id', $user->id)->first() ?: new SellerKycDetail();
            $kyc->user_id = $user->id;
            $kyc->identity_type = 'pan';
            $kyc->identity_number = $record->pan_number;
            $kyc->gst_number = $record->gst_number;
            $kyc->address = $record->company_address;
            $kyc->postal_code = $record->pincode;
            $kyc->city = $record->city;
            $kyc->state = $record->state;
            $kyc->country = $record->country;
            $kyc->account_number = $record->account_number;
            $kyc->account_holder_name = $record->account_holder_name;
            $kyc->bank_name = $record->bank_name;
            $kyc->ifsc_code = $record->ifsc_code;
            $kyc->identity_proof = $record->pan_document_path ?: $kyc->identity_proof;
            $kyc->address_proof = $record->address_proof_path ?: $kyc->address_proof;
            $kyc->business_registration_certificate = $record->registration_certificate_path ?: $kyc->business_registration_certificate;
            $kyc->bank_proof = $record->cancelled_cheque_path ?: $kyc->bank_proof;
            $kyc->trademark_registration_proof = $record->other_documents_path ?: $kyc->trademark_registration_proof;
            $kyc->status = 'approved';
            $kyc->save();

            $detail = UserDetail::where('user_id', $user->id)->first() ?: new UserDetail();
            $detail->user_id = $user->id;
            $detail->company_name = $record->company_name;
            $detail->gst_number = $record->gst_number;
            $detail->pan_number = $record->pan_number;
            $detail->company_address = $record->company_address;
            $detail->city = $record->city;
            $detail->state = $record->state;
            $detail->country = $record->country;
            $detail->postal_code = $record->pincode;
            $detail->save();

            $record->request_status = 'approved';
            $record->reviewed_at = now();
            $record->reviewed_by = auth('admin')->id();
            $record->review_note = $this->reviewNote ?: null;
            $record->appendTimeline('approved', [
                'reviewed_by' => auth('admin')->id(),
                'note' => $this->reviewNote ?: null,
            ]);
            $record->save();
        });

        $this->requestRecord = $this->requestRecord->fresh(['getUser', 'getReviewer']);

        if ($this->requestRecord->getUser?->email) {
            Mail::to($this->requestRecord->getUser->email)->send(new SellerRequestStatusChangedMail($this->requestRecord));
        }

        session()->flash('success', 'Seller request approved and user promoted to seller.');
    }

    public function reject(): void
    {
        $this->authorize('seller-list');

        $this->validate([
            'reviewNote' => ['required', 'string', 'max:2000'],
        ]);

        $record = $this->requestRecord->fresh(['getUser', 'getReviewer']);
        if ($record->request_status === 'approved') {
            $this->addError('reviewNote', 'Approved requests cannot be rejected.');
            return;
        }

        DB::transaction(function () use ($record) {
            $record->request_status = 'rejected';
            $record->reviewed_at = now();
            $record->reviewed_by = auth('admin')->id();
            $record->review_note = $this->reviewNote;
            $record->appendTimeline('rejected', [
                'reviewed_by' => auth('admin')->id(),
                'note' => $this->reviewNote,
            ]);
            $record->save();
        });

        $this->requestRecord = $this->requestRecord->fresh(['getUser', 'getReviewer']);

        if ($this->requestRecord->getUser?->email) {
            Mail::to($this->requestRecord->getUser->email)->send(new SellerRequestStatusChangedMail($this->requestRecord));
        }

        session()->flash('success', 'Seller request rejected.');
    }

    public function render()
    {
        return view('admin.seller_request.show', [
            'page_title' => 'Review Seller Request',
        ]);
    }
}
