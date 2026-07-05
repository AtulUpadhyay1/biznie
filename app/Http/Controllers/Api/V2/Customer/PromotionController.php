<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\ProfileResource;
use App\Mail\SellerRequestSubmittedAdminMail;
use App\Mail\SellerRequestSubmittedUserMail;
use App\Models\SellerKycDetail;
use App\Models\SellerOnboardingDetail;
use App\Models\SellerType;
use App\Models\TransporterDetail;
use App\Models\UserPromotionHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function sellerTypes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => SellerType::active()->get(['id', 'name']),
        ]);
    }

    public function sellerRequest(Request $request): JsonResponse
    {
        $record = $this->getOrCreateRequest($request->user(), false);

        return response()->json([
            'success' => true,
            'data'    => $record ? $this->requestPayload($record) : null,
        ]);
    }

    public function saveSellerRequestStep(Request $request): JsonResponse
    {
        $user = $request->user();
        $record = $this->getOrCreateRequest($user, true);

        if ($record->request_status === 'approved' || $record->request_status === 'pending_review') {
            return response()->json([
                'success' => false,
                'message' => 'This request is locked while under review or approved.',
            ], 422);
        }

        $step = max(1, min(5, (int) $request->input('step', 1)));
        $data = $request->validate($this->stepRules($step, $user));

        DB::transaction(function () use ($request, $record, $data, $step) {
            $this->applyRequestData($record, $request, $data);
            $record->current_step = max((int) ($record->current_step ?? 1), $step);
            $record->request_status = $record->request_status === 'rejected' ? 'draft' : 'draft';
            $this->appendTimeline($record, 'step_saved', [
                'step' => $step,
                'label' => $this->stepLabel($step),
            ]);
            $record->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Step saved successfully.',
            'data' => $this->requestPayload($record->fresh()),
        ]);
    }

    public function submitSellerRequest(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->type === 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'You are already a seller.',
            ], 400);
        }

        $record = $this->getOrCreateRequest($user, true);

        if ($record->request_status === 'approved' || $record->request_status === 'pending_review') {
            return response()->json([
                'success' => false,
                'message' => 'This seller request is already submitted for review.',
            ], 422);
        }

        $data = $request->validate($this->submissionRules($user));

        DB::transaction(function () use ($request, $record, $data) {
            $this->applyRequestData($record, $request, $data);

            if (! $record->request_reference) {
                $record->request_reference = $this->generateReference();
            }

            $record->request_status = 'pending_review';
            $record->submitted_at = now();
            $record->reviewed_at = null;
            $record->reviewed_by = null;
            $record->review_note = null;
            $record->current_step = 6;

            $this->appendTimeline($record, 'submitted', [
                'reference' => $record->request_reference,
            ]);

            $record->save();

            $this->sendSubmissionNotifications($record);
        });

        return response()->json([
            'success' => true,
            'message' => 'Seller request submitted successfully. Our team will review it shortly.',
            'data' => $this->requestPayload($record->fresh()),
        ]);
    }

    public function becomeSeller(Request $request): JsonResponse
    {
        return $this->submitSellerRequest($request);
    }

    private function getOrCreateRequest($user, bool $create = true): ?SellerOnboardingDetail
    {
        $record = SellerOnboardingDetail::where('user_id', $user->id)->first();

        if (! $record && $create) {
            $record = new SellerOnboardingDetail();
            $record->user_id = $user->id;
            $record->request_status = 'draft';
            $record->current_step = 1;
            $record->timeline = [];
            $record->save();
        }

        return $record;
    }

    private function stepRules(int $step, $user): array
    {
        return match ($step) {
            1 => [
                'company_name' => ['required', 'string', 'max:160'],
                'gst_number' => ['required', 'string', 'max:30'],
                'pan_number' => ['required', 'string', 'max:20', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i'],
                'seller_type_id' => ['required', 'integer', Rule::exists('seller_types', 'id')],
                'business_type' => ['nullable', 'string', 'max:120'],
                'company_address' => ['required', 'string', 'max:500'],
                'constitution_type' => ['required', 'string', 'max:120'],
                'years_in_business' => ['required', 'integer', 'min:0', 'max:100'],
                'city' => ['required', 'string', 'max:120'],
                'state' => ['required', 'string', 'max:120'],
                'country' => ['required', 'string', 'max:120'],
                'pincode' => ['required', 'regex:/^\d{6}$/'],
            ],
            2 => [
                'contact_person' => ['required', 'string', 'max:120'],
                'designation' => ['required', 'string', 'max:120'],
                'mobile' => ['required', 'regex:/^\d{10}$/', Rule::unique('users', 'phone')->ignore($user->id)],
                'alternate_mobile' => ['nullable', 'regex:/^\d{10}$/'],
                'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            ],
            3 => [
                'gst_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'pan_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'registration_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'address_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'cancelled_cheque' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'other_documents' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ],
            4 => [
                'account_holder_name' => ['required', 'string', 'max:160'],
                'bank_name' => ['required', 'string', 'max:160'],
                'account_number' => ['required', 'regex:/^\d{9,18}$/'],
                'ifsc_code' => ['required', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
                'account_type' => ['required', 'string', 'max:40'],
                'branch' => ['required', 'string', 'max:160'],
            ],
            5 => [
                'category' => ['required', 'string', 'max:160'],
                'products' => ['required', 'string', 'max:4000'],
                'turnover' => ['required', 'string', 'max:120'],
                'product_upload_mode' => ['required', Rule::in(['existing', 'new', 'bulk'])],
            ],
            default => throw new \InvalidArgumentException('Invalid seller step.'),
        };
    }

    private function submissionRules($user): array
    {
        return [
            'company_name' => ['required', 'string', 'max:160'],
            'gst_number' => ['required', 'string', 'max:30'],
            'pan_number' => ['required', 'string', 'max:20', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i'],
            'seller_type_id' => ['required', 'integer', Rule::exists('seller_types', 'id')],
            'business_type' => ['nullable', 'string', 'max:120'],
            'company_address' => ['required', 'string', 'max:500'],
            'constitution_type' => ['required', 'string', 'max:120'],
            'years_in_business' => ['required', 'integer', 'min:0', 'max:100'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'pincode' => ['required', 'regex:/^\d{6}$/'],

            'contact_person' => ['required', 'string', 'max:120'],
            'designation' => ['required', 'string', 'max:120'],
            'mobile' => ['required', 'regex:/^\d{10}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'alternate_mobile' => ['nullable', 'regex:/^\d{10}$/'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],

            'gst_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'pan_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'registration_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'address_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'cancelled_cheque' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'other_documents' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],

            'account_holder_name' => ['required', 'string', 'max:160'],
            'bank_name' => ['required', 'string', 'max:160'],
            'account_number' => ['required', 'regex:/^\d{9,18}$/'],
            'ifsc_code' => ['required', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
            'account_type' => ['required', 'string', 'max:40'],
            'branch' => ['required', 'string', 'max:160'],

            'category' => ['required', 'string', 'max:160'],
            'products' => ['required', 'string', 'max:4000'],
            'turnover' => ['required', 'string', 'max:120'],
            'product_upload_mode' => ['required', Rule::in(['existing', 'new', 'bulk'])],
        ];
    }

    private function applyRequestData(SellerOnboardingDetail $record, Request $request, array $data): void
    {
        foreach (['company_name', 'gst_number', 'pan_number', 'company_address', 'constitution_type', 'years_in_business', 'city', 'state', 'country', 'pincode', 'contact_person', 'designation', 'mobile', 'alternate_mobile', 'email', 'account_holder_name', 'bank_name', 'account_number', 'ifsc_code', 'account_type', 'branch', 'category', 'products', 'turnover', 'product_upload_mode', 'business_type'] as $field) {
            if (array_key_exists($field, $data)) {
                $record->{$field} = $data[$field] ?: null;
            }
        }

        if (array_key_exists('seller_type_id', $data)) {
            $record->seller_type_id = $data['seller_type_id'];
            if (empty($record->business_type)) {
                $record->business_type = SellerType::find($data['seller_type_id'])?->name;
            }
        }

        foreach (['gst_certificate', 'pan_document', 'registration_certificate', 'address_proof', 'cancelled_cheque', 'other_documents'] as $field) {
            if ($request->hasFile($field)) {
                $column = $this->documentColumn($field);
                $record->{$column} = $this->storeSellerDocument($request, $field, $record->{$column} ?? null);
            }
        }
    }

    private function requestPayload(SellerOnboardingDetail $record): array
    {
        return [
            'id' => $record->id,
            'request_reference' => $record->request_reference,
            'request_status' => $record->request_status,
            'current_step' => $record->current_step,
            'can_edit' => in_array($record->request_status, ['draft', 'rejected'], true),
            'submitted_at' => optional($record->submitted_at)->toIso8601String(),
            'reviewed_at' => optional($record->reviewed_at)->toIso8601String(),
            'review_note' => $record->review_note,
            'timeline' => $record->timeline ?? [],
            'data' => [
                'company_name' => $record->company_name,
                'gst_number' => $record->gst_number,
                'pan_number' => $record->pan_number,
                'seller_type_id' => $record->seller_type_id,
                'business_type' => $record->business_type,
                'company_address' => $record->company_address,
                'constitution_type' => $record->constitution_type,
                'years_in_business' => $record->years_in_business,
                'city' => $record->city,
                'state' => $record->state,
                'country' => $record->country,
                'pincode' => $record->pincode,
                'contact_person' => $record->contact_person,
                'designation' => $record->designation,
                'mobile' => $record->mobile,
                'alternate_mobile' => $record->alternate_mobile,
                'email' => $record->email,
                'gst_certificate' => $this->documentUrl($record->gst_certificate_path),
                'pan_document' => $this->documentUrl($record->pan_document_path),
                'registration_certificate' => $this->documentUrl($record->registration_certificate_path),
                'address_proof' => $this->documentUrl($record->address_proof_path),
                'cancelled_cheque' => $this->documentUrl($record->cancelled_cheque_path),
                'other_documents' => $this->documentUrl($record->other_documents_path),
                'account_holder_name' => $record->account_holder_name,
                'bank_name' => $record->bank_name,
                'account_number' => $record->account_number,
                'ifsc_code' => $record->ifsc_code,
                'account_type' => $record->account_type,
                'branch' => $record->branch,
                'category' => $record->category,
                'products' => $record->products,
                'turnover' => $record->turnover,
                'product_upload_mode' => $record->product_upload_mode,
            ],
        ];
    }

    private function appendTimeline(SellerOnboardingDetail $record, string $event, array $meta = []): void
    {
        $timeline = $record->timeline ?? [];
        $timeline[] = array_merge([
            'event' => $event,
            'label' => $this->timelineLabel($event),
            'at' => now()->toIso8601String(),
        ], $meta);
        $record->timeline = $timeline;
    }

    private function timelineLabel(string $event): string
    {
        return match ($event) {
            'step_saved' => 'Step saved',
            'submitted' => 'Request submitted',
            'approved' => 'Request approved',
            'rejected' => 'Request rejected',
            'resubmitted' => 'Request resubmitted',
            default => $event,
        };
    }

    private function stepLabel(int $step): string
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

    private function documentUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    private function documentColumn(string $field): string
    {
        return match ($field) {
            'gst_certificate' => 'gst_certificate_path',
            'pan_document' => 'pan_document_path',
            'registration_certificate' => 'registration_certificate_path',
            'address_proof' => 'address_proof_path',
            'cancelled_cheque' => 'cancelled_cheque_path',
            'other_documents' => 'other_documents_path',
            default => throw new \InvalidArgumentException('Invalid document field.'),
        };
    }

    private function storeSellerDocument(Request $request, string $field, ?string $existingPath = null): ?string
    {
        if (! $request->hasFile($field)) {
            return $existingPath;
        }

        $newPath = $request->file($field)->store('seller-onboarding/'.$request->user()->id, 'public');

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $newPath;
    }

    public function becomeTransporter(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->type === 'transporter') {
            return response()->json([
                'success' => false,
                'message' => 'You are already a transporter.',
            ], 400);
        }

        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120'],
            'phone'           => ['required', 'string', 'regex:/^\d{10}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'company_name'    => ['required', 'string', 'max:160'],
            'gst_number'      => ['nullable', 'string', 'max:30'],
            'address'         => ['nullable', 'string', 'max:500'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'aadhar_number'   => ['nullable', 'string', 'max:20'],
        ]);

        $user->name  = $data['name'];
        $user->phone = $data['phone'];
        $user->type  = 'transporter';
        $user->save();

        $log = new UserPromotionHistory();
        $log->user_id  = $user->id;
        $log->old_type = 'customer';
        $log->new_type = 'transporter';
        $log->save();

        $detail = TransporterDetail::where('user_id', $user->id)->first() ?: new TransporterDetail();
        $detail->user_id         = $user->id;
        $detail->company_name    = $data['company_name'];
        $detail->gst_number      = $data['gst_number'] ?? $detail->gst_number;
        $detail->address         = $data['address'] ?? $detail->address;
        $detail->alternate_phone = $data['alternate_phone'] ?? $detail->alternate_phone;
        $detail->aadhar_number   = $data['aadhar_number'] ?? $detail->aadhar_number;
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Congratulations, you are now a transporter.',
            'data'    => new ProfileResource($user->fresh(['getUserDetail', 'getSellerKycDetail', 'getTransporterDetail'])),
        ]);
    }

}
