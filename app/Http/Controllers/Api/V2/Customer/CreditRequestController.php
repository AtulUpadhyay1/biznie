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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'credit_wallet_document_type_id' => ['required', 'integer', 'exists:credit_wallet_document_types,id'],
            'form_data'                      => ['required', 'array'],
            'document_type'                  => ['nullable', 'array'],
            'document'                       => ['nullable'],
            'notes'                          => ['nullable', 'string', 'max:2000'],
            'description'                    => ['nullable', 'string', 'max:5000'],
        ]);

        $userId = $this->ownerId($request);

        $req = new CreditWalletRequest();
        $req->user_id                        = $userId;
        $req->credit_wallet_document_type_id = $data['credit_wallet_document_type_id'];
        $req->form_data                      = $data['form_data'];
        $req->document_type                  = $data['document_type'] ?? [];
        $req->document                       = $data['document'] ?? [];
        $req->notes                          = $data['notes'] ?? null;
        $req->description                    = $data['description'] ?? null;
        $req->reference_number               = 'CR-'.date('Ymd').'-'.rand(1111, 9999);
        $req->status                         = 'pending';
        $req->save();

        return response()->json([
            'success' => true,
            'message' => 'Credit request submitted successfully.',
            'data'    => new CreditRequestResource($req->load('getDocumentType')),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);

        $req = CreditWalletRequest::where('user_id', $userId)->find($id);

        if (! $req) {
            return response()->json(['success' => false, 'message' => 'Credit request not found.'], 404);
        }

        if ($req->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This request can no longer be edited.',
            ], 422);
        }

        $data = $request->validate([
            'form_data'   => ['sometimes', 'array'],
            'document'    => ['sometimes'],
            'notes'       => ['sometimes', 'nullable', 'string', 'max:2000'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ]);

        if (array_key_exists('form_data', $data))   $req->form_data   = $data['form_data'];
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

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
