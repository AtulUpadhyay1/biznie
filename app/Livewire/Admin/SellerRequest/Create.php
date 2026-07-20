<?php

namespace App\Livewire\Admin\SellerRequest;

use App\Mail\SellerRequestSubmittedAdminMail;
use App\Mail\SellerRequestSubmittedUserMail;
use App\Models\SellerOnboardingDetail;
use App\Models\SellerType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Raise a "become a seller" request on behalf of a buyer from the admin panel.
 *
 * Mirrors the buyer-side wizard in Api\V2\Customer\PromotionController: the same
 * five steps, the same validation and the same columns on
 * seller_onboarding_details, so both entry points produce identical records.
 */
class Create extends Component
{
    use WithFileUploads;

    /** Total wizard steps (1..5 collect + submit). */
    private const TOTAL_STEPS = 5;

    public User $buyer;
    public ?int $recordId = null;
    public int $step = 1;
    public int $maxStep = 1;

    /* Step 1 - Business Information */
    public $company_name = '';
    public $gst_number = '';
    public $pan_number = '';
    public $seller_type_id = '';
    public $business_type = '';
    public $company_address = '';
    public $constitution_type = '';
    public $years_in_business = '';
    public $city = '';
    public $state = '';
    public $country = 'India';
    public $pincode = '';

    /* Step 2 - Contact Person */
    public $contact_person = '';
    public $designation = '';
    public $mobile = '';
    public $alternate_mobile = '';
    public $email = '';

    /* Step 3 - Business Documents */
    public $gst_certificate;
    public $pan_document;
    public $registration_certificate;
    public $address_proof;
    public $cancelled_cheque;
    public $other_documents;
    public array $storedDocuments = [];

    /* Step 4 - Bank Details */
    public $account_holder_name = '';
    public $bank_name = '';
    public $account_number = '';
    public $ifsc_code = '';
    public $account_type = '';
    public $branch = '';

    /* Step 5 - Store & Products */
    public $category = '';
    public $products = '';
    public $turnover = '';
    public $product_upload_mode = 'existing';

    /** Request field => stored path column. */
    private const DOCUMENT_FIELDS = [
        'gst_certificate'          => 'gst_certificate_path',
        'pan_document'             => 'pan_document_path',
        'registration_certificate' => 'registration_certificate_path',
        'address_proof'            => 'address_proof_path',
        'cancelled_cheque'         => 'cancelled_cheque_path',
        'other_documents'          => 'other_documents_path',
    ];

    public function mount($user_id): void
    {
        $this->authorize('seller-list');

        $this->buyer = User::where('type', 'customer')->findOrFail($user_id);

        // Pre-fill from the buyer's account and resume an existing draft, exactly
        // as the buyer-side wizard would.
        $this->contact_person = $this->buyer->name ?? '';
        $this->mobile = $this->buyer->phone ?? '';
        $this->email = $this->buyer->email ?? '';

        $record = SellerOnboardingDetail::where('user_id', $this->buyer->id)->first();
        if ($record) {
            $this->recordId = $record->id;
            $this->maxStep = min(self::TOTAL_STEPS + 1, max(1, (int) $record->current_step));
            $this->fillFromRecord($record);
        }
    }

    /* --------------------------------------------------------------------- */
    /* Wizard navigation                                                     */
    /* --------------------------------------------------------------------- */

    public function nextStep(): void
    {
        $this->saveStep();

        if (! $this->getErrorBag()->isEmpty()) {
            return;
        }

        if ($this->step < self::TOTAL_STEPS + 1) {
            $this->step++;
            $this->maxStep = max($this->maxStep, $this->step);
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->maxStep) {
            $this->step = $step;
        }
    }

    /**
     * Persist the current step onto the draft record (created on first save).
     */
    public function saveStep(): void
    {
        if ($this->step > self::TOTAL_STEPS) {
            return;
        }

        $record = $this->record();

        if ($record && in_array($record->request_status, ['pending_review', 'approved'], true)) {
            $this->addError('step', 'This request is locked while under review or approved.');
            $this->dispatch('alert', type: 'error', message: 'This request is locked while under review or approved.');

            return;
        }

        $this->validate($this->stepRules($this->step), [], $this->validationAttributes());

        $record = $record ?: $this->newDraft();
        $step = $this->step;

        DB::transaction(function () use ($record, $step) {
            $this->applyStepData($record, $step);
            $record->current_step = max((int) ($record->current_step ?? 1), $step);
            $record->request_status = 'draft';
            $record->appendTimeline('step_saved', [
                'step'  => $step,
                'label' => $this->stepLabel($step),
                'by'    => 'admin',
            ]);
            $record->save();
        });

        $this->recordId = $record->id;
        $this->refreshStoredDocuments($record->fresh());
        $this->resetDocumentUploads();
    }

    /**
     * Finalise the draft and push it into the review queue.
     */
    public function submit()
    {
        $this->authorize('seller-list');

        if ($this->buyer->type === 'seller') {
            $this->dispatch('alert', type: 'error', message: 'This user is already a seller.');

            return null;
        }

        $record = $this->record();

        if (! $record) {
            $this->dispatch('alert', type: 'error', message: 'Please complete the wizard before submitting.');

            return null;
        }

        if (in_array($record->request_status, ['pending_review', 'approved'], true)) {
            $this->dispatch('alert', type: 'error', message: 'This seller request is already submitted for review.');

            return null;
        }

        // Re-validate every collected step so a partially filled draft cannot be
        // pushed into the review queue.
        foreach (range(1, self::TOTAL_STEPS) as $step) {
            $this->validate($this->stepRules($step), [], $this->validationAttributes());
        }

        DB::transaction(function () use ($record) {
            if (! $record->request_reference) {
                $record->request_reference = $this->generateReference();
            }

            $record->request_status = 'pending_review';
            $record->submitted_at = now();
            $record->reviewed_at = null;
            $record->reviewed_by = null;
            $record->review_note = null;
            $record->current_step = self::TOTAL_STEPS + 1;
            $record->appendTimeline('submitted', [
                'reference' => $record->request_reference,
                'by'        => 'admin',
            ]);
            $record->save();

            $this->sendSubmissionNotifications($record);
        });

        session()->flash('success', 'Seller request submitted successfully on behalf of '.$this->buyer->name.'.');

        return redirect()->route('admin.seller-request.index');
    }

    /* --------------------------------------------------------------------- */
    /* Helpers                                                               */
    /* --------------------------------------------------------------------- */

    private function record(): ?SellerOnboardingDetail
    {
        if (! $this->recordId) {
            return null;
        }

        return SellerOnboardingDetail::where('user_id', $this->buyer->id)->find($this->recordId);
    }

    private function newDraft(): SellerOnboardingDetail
    {
        $record = new SellerOnboardingDetail();
        $record->user_id = $this->buyer->id;
        $record->request_status = 'draft';
        $record->current_step = 1;
        $record->timeline = [];
        $record->save();

        return $record;
    }

    private function fillFromRecord(SellerOnboardingDetail $record): void
    {
        foreach ([
            'company_name', 'gst_number', 'pan_number', 'business_type', 'company_address',
            'constitution_type', 'years_in_business', 'city', 'state', 'country', 'pincode',
            'contact_person', 'designation', 'mobile', 'alternate_mobile', 'email',
            'account_holder_name', 'bank_name', 'account_number', 'ifsc_code', 'account_type',
            'branch', 'category', 'products', 'turnover',
        ] as $field) {
            if ($record->{$field} !== null && $record->{$field} !== '') {
                $this->{$field} = $record->{$field};
            }
        }

        $this->seller_type_id = $record->seller_type_id ?: '';
        $this->product_upload_mode = $record->product_upload_mode ?: 'existing';

        $this->refreshStoredDocuments($record);
    }

    private function refreshStoredDocuments(?SellerOnboardingDetail $record): void
    {
        $this->storedDocuments = [];

        if (! $record) {
            return;
        }

        foreach (self::DOCUMENT_FIELDS as $field => $column) {
            if ($record->{$column}) {
                $this->storedDocuments[$field] = Storage::disk('public')->url($record->{$column});
            }
        }
    }

    private function resetDocumentUploads(): void
    {
        foreach (array_keys(self::DOCUMENT_FIELDS) as $field) {
            $this->{$field} = null;
        }
    }

    private function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'company_name'      => ['required', 'string', 'max:160'],
                'gst_number'        => ['required', 'string', 'max:30'],
                'pan_number'        => ['required', 'string', 'max:20', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i'],
                'seller_type_id'    => ['required', 'integer', Rule::exists('seller_types', 'id')],
                'business_type'     => ['nullable', 'string', 'max:120'],
                'company_address'   => ['required', 'string', 'max:500'],
                'constitution_type' => ['required', 'string', 'max:120'],
                'years_in_business' => ['required', 'integer', 'min:0', 'max:100'],
                'city'              => ['required', 'string', 'max:120'],
                'state'             => ['required', 'string', 'max:120'],
                'country'           => ['required', 'string', 'max:120'],
                'pincode'           => ['required', 'regex:/^\d{6}$/'],
            ],
            2 => [
                'contact_person'   => ['required', 'string', 'max:120'],
                'designation'      => ['required', 'string', 'max:120'],
                'mobile'           => ['required', 'regex:/^\d{10}$/', Rule::unique('users', 'phone')->ignore($this->buyer->id)],
                'alternate_mobile' => ['nullable', 'regex:/^\d{10}$/'],
                'email'            => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($this->buyer->id)],
            ],
            3 => [
                'gst_certificate'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'pan_document'             => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'registration_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'address_proof'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'cancelled_cheque'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'other_documents'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ],
            4 => [
                'account_holder_name' => ['required', 'string', 'max:160'],
                'bank_name'           => ['required', 'string', 'max:160'],
                'account_number'      => ['required', 'regex:/^\d{9,18}$/'],
                'ifsc_code'           => ['required', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
                'account_type'        => ['required', 'string', 'max:40'],
                'branch'              => ['required', 'string', 'max:160'],
            ],
            5 => [
                'category'            => ['required', 'string', 'max:160'],
                'products'            => ['required', 'string', 'max:4000'],
                'turnover'            => ['required', 'string', 'max:120'],
                'product_upload_mode' => ['required', Rule::in(['existing', 'new', 'bulk'])],
            ],
            default => [],
        };
    }

    private function validationAttributes(): array
    {
        return [
            'company_name'             => 'company name',
            'gst_number'               => 'GST number',
            'pan_number'               => 'PAN number',
            'seller_type_id'           => 'seller type',
            'business_type'            => 'business type',
            'company_address'          => 'company address',
            'constitution_type'        => 'constitution type',
            'years_in_business'        => 'years in business',
            'contact_person'           => 'contact person',
            'alternate_mobile'         => 'alternate mobile',
            'gst_certificate'          => 'GST certificate',
            'pan_document'             => 'PAN document',
            'registration_certificate' => 'registration certificate',
            'address_proof'            => 'address proof',
            'cancelled_cheque'         => 'cancelled cheque',
            'other_documents'          => 'other documents',
            'account_holder_name'      => 'account holder name',
            'bank_name'                => 'bank name',
            'account_number'           => 'account number',
            'ifsc_code'                => 'IFSC code',
            'account_type'             => 'account type',
            'product_upload_mode'      => 'product upload mode',
        ];
    }

    /** Maps the wizard fields of one step onto the record columns. */
    private function applyStepData(SellerOnboardingDetail $record, int $step): void
    {
        switch ($step) {
            case 1:
                foreach (['company_name', 'gst_number', 'pan_number', 'company_address', 'constitution_type', 'years_in_business', 'city', 'state', 'country', 'pincode', 'business_type'] as $field) {
                    $record->{$field} = $this->{$field} ?: null;
                }
                $record->seller_type_id = $this->seller_type_id;
                if (empty($record->business_type)) {
                    $record->business_type = SellerType::find($this->seller_type_id)?->name;
                }
                break;

            case 2:
                foreach (['contact_person', 'designation', 'mobile', 'alternate_mobile', 'email'] as $field) {
                    $record->{$field} = $this->{$field} ?: null;
                }
                break;

            case 3:
                foreach (self::DOCUMENT_FIELDS as $field => $column) {
                    if ($this->{$field}) {
                        $record->{$column} = $this->storeSellerDocument($this->{$field}, $record->{$column} ?? null);
                    }
                }
                break;

            case 4:
                foreach (['account_holder_name', 'bank_name', 'account_number', 'ifsc_code', 'account_type', 'branch'] as $field) {
                    $record->{$field} = $this->{$field} ?: null;
                }
                break;

            case 5:
                foreach (['category', 'products', 'turnover', 'product_upload_mode'] as $field) {
                    $record->{$field} = $this->{$field} ?: null;
                }
                break;
        }
    }

    private function storeSellerDocument($file, ?string $existingPath = null): string
    {
        $newPath = $file->store('seller-onboarding/'.$this->buyer->id, 'public');

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $newPath;
    }

    public function stepLabel(int $step): string
    {
        return match ($step) {
            1 => 'Business Information',
            2 => 'Contact Person',
            3 => 'Business Documents',
            4 => 'Bank Details',
            5 => 'Store & Products',
            default => 'Review & Submit',
        };
    }

    private function generateReference(): string
    {
        return 'SR-'.date('Ymd').'-'.Str::upper(Str::random(6));
    }

    private function sendSubmissionNotifications(SellerOnboardingDetail $record): void
    {
        $record->loadMissing('getUser');

        Mail::to('contact@biznie.com')->send(new SellerRequestSubmittedAdminMail($record));

        if ($record->getUser?->email) {
            Mail::to($record->getUser->email)->send(new SellerRequestSubmittedUserMail($record));
        }
    }

    public function render()
    {
        $sellerTypes = SellerType::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.seller_request.create', compact('sellerTypes'), [
            'page_title' => 'Add Seller Request',
        ]);
    }
}
