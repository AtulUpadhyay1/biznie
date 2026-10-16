<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\CreditDocumentTypeResource;
use App\Http\Resources\V2\CreditRequestResource;
use App\Models\CreditWalletDocumentType;
use App\Models\CreditWalletRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditRequestController extends Controller
{
    public function documentTypes(): JsonResponse
    {
        $types = CreditWalletDocumentType::active()->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data'    => CreditDocumentTypeResource::collection($types)->resolve(),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $userId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = CreditWalletRequest::where('user_id', $userId)
            ->with(['getDocumentType'])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => CreditRequestResource::collection($list)->resolve(),
            'meta'    => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);

        $req = CreditWalletRequest::where('user_id', $userId)
            ->with(['getDocumentType'])
            ->find($id);

        if (! $req) {
            return response()->json(['success' => false, 'message' => 'Credit request not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new CreditRequestResource($req),
        ]);
    }

    /**
     * A credit request: which facility, how much, for how long, and the
     * documents that facility's type asks for (B30).
     *
     * Multipart: `fields[i]` holds the text answer and `files[i]` the upload
     * for the i-th entry of the type's `forms`. Older clients that post a
     * ready-made `form_data` array are still accepted.
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $this->ownerId($request);

        // One open request at a time: a second one used to be accepted and
        // sat beside the first in the admin queue.
        if (CreditWalletRequest::where('user_id', $userId)->whereRaw('LOWER(status) = ?', ['pending'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a credit request under review. We will update you on it before you can raise another.',
            ], 409);
        }

        $data = $request->validate([
            'credit_wallet_document_type_id' => ['required', 'integer', 'exists:credit_wallet_document_types,id'],
            'amount'                         => ['required', 'numeric', 'min:100000', 'max:500000000'],
            'tenure_days'                    => ['required', 'integer', 'in:30,60,90'],
            'notes'                          => ['nullable', 'string', 'max:2000'],
            'description'                    => ['nullable', 'string', 'max:5000'],
            'form_data'                      => ['nullable', 'array'],
        ], [
            'amount.min' => 'Minimum credit limit you can request is ₹1,00,000',
        ]);

        $type = CreditWalletDocumentType::findOrFail($data['credit_wallet_document_type_id']);
        $formData = $request->has('form_data') && ! $request->hasFile('files')
            ? $data['form_data']
            : $this->collectFormData($request, $type);

        $req = new CreditWalletRequest();
        $req->user_id                        = $userId;
        $req->credit_wallet_document_type_id = $type->id;
        $req->amount                         = $data['amount'];
        $req->tenure_days                    = (int) $data['tenure_days'];
        $req->form_data                      = $formData;
        $req->document_type                  = [$type->name ?: $type->title];
        $req->document                       = [];
        $req->notes                          = $data['notes'] ?? null;
        $req->description                    = $data['description'] ?? null;
        $req->reference_number               = $this->nextReference();
        // Title case, as the admin screens read and write it.
        $req->status                         = 'Pending';
        $req->save();

        sendAdminNotification(
            'New credit request',
            ($request->user()->name ?? 'A buyer') . ' requested a ₹' . formatIndianNumber($req->amount) . ' credit limit for ' . $req->tenure_days . ' days',
            'credit_request',
            ['id' => $req->id, 'reference' => $req->reference_number]
        );

        return response()->json([
            'success' => true,
            'message' => 'Credit request submitted successfully.',
            'data'    => new CreditRequestResource($req->load('getDocumentType')),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);

        $req = CreditWalletRequest::where('user_id', $userId)->with('getDocumentType')->find($id);

        if (! $req) {
            return response()->json(['success' => false, 'message' => 'Credit request not found.'], 404);
        }

        // Admin writes "Pending"; this compared against lower-case "pending"
        // and so refused every edit.
        if (strcasecmp((string) $req->status, 'pending') !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'This request can no longer be edited.',
            ], 422);
        }

        $data = $request->validate([
            'amount'      => ['sometimes', 'numeric', 'min:100000', 'max:500000000'],
            'tenure_days' => ['sometimes', 'integer', 'in:30,60,90'],
            'form_data'   => ['sometimes', 'array'],
            'document'    => ['sometimes'],
            'notes'       => ['sometimes', 'nullable', 'string', 'max:2000'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ], [
            'amount.min' => 'Minimum credit limit you can request is ₹1,00,000',
        ]);

        if (array_key_exists('amount', $data))      $req->amount      = $data['amount'];
        if (array_key_exists('tenure_days', $data)) $req->tenure_days = (int) $data['tenure_days'];
        if ($request->hasFile('files') || $request->has('fields')) {
            $req->form_data = $this->collectFormData($request, $req->getDocumentType, is_array($req->form_data) ? $req->form_data : []);
        } elseif (array_key_exists('form_data', $data)) {
            $req->form_data = $data['form_data'];
        }
        if (array_key_exists('document', $data))    $req->document    = $data['document'];
        if (array_key_exists('notes', $data))       $req->notes       = $data['notes'];
        if (array_key_exists('description', $data)) $req->description = $data['description'];
        $req->save();

        return response()->json([
            'success' => true,
            'message' => 'Credit request updated.',
            'data'    => new CreditRequestResource($req->load('getDocumentType')),
        ]);
    }

    /**
     * Builds form_data in the shape the admin screen and the mobile app read
     * (label / required / type / description / value), uploading each file.
     * On edit, a field left empty keeps its previous value.
     */
    private function collectFormData(Request $request, ?CreditWalletDocumentType $type, array $previous = []): array
    {
        $forms = $type && is_array($type->forms) ? $type->forms : (json_decode((string) $type?->forms, true) ?: []);
        $errors = [];
        $out = [];

        foreach (array_values($forms) as $i => $field) {
            $kind = $field['type'] ?? 'text';
            $required = filter_var($field['required'] ?? false, FILTER_VALIDATE_BOOL);
            $label = trim((string) ($field['label'] ?? 'Document'));
            $value = $previous[$i]['value'] ?? null;

            if ($kind === 'file') {
                $file = $request->file("files.{$i}");
                if ($file) {
                    if (! in_array(strtolower($file->getClientOriginalExtension()), ['pdf', 'jpg', 'jpeg', 'png'], true) || $file->getSize() > 10 * 1024 * 1024) {
                        $errors["files.{$i}"] = ["{$label}: upload a PDF, JPG or PNG up to 10 MB"];
                        continue;
                    }
                    $value = (string) imageUpload($file, 'credit-requests');
                }
            } else {
                $text = trim((string) $request->input("fields.{$i}", ''));
                if ($text !== '') {
                    $value = mb_substr($text, 0, 500);
                }
            }

            if ($required && ($value === null || $value === '')) {
                $errors[$kind === 'file' ? "files.{$i}" : "fields.{$i}"] = ["{$label} is required"];
            }

            $out[] = [
                'label'       => $label,
                'required'    => $required ? 'true' : 'false',
                'type'        => $kind,
                'description' => $field['description'] ?? null,
                'value'       => $value,
            ];
        }

        if ($errors) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }

        return $out;
    }

    /** `CR-20261016-4821`, unique. */
    private function nextReference(): string
    {
        do {
            $ref = 'CR-' . date('Ymd') . '-' . random_int(1000, 9999);
        } while (CreditWalletRequest::where('reference_number', $ref)->exists());

        return $ref;
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
