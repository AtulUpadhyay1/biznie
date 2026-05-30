<?php

namespace App\Http\Controllers\Api\V2\Transporter;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Transporter\TransporterEnquiryDetailResource;
use App\Http\Resources\V2\Transporter\TransporterEnquiryResource;
use App\Models\TransporterProductEnquiry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = TransporterProductEnquiry::where('user_id', $ownerId)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,category_id',
                'getCommodityProduct.getCategory:id,name',
                'getProductEnquiry:id,unique_id,status',
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => TransporterEnquiryResource::collection($list)->resolve(),
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
        $ownerId = $this->ownerId($request);
        $enquiry = TransporterProductEnquiry::where('user_id', $ownerId)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,category_id',
                'getCommodityProduct.getCategory:id,name',
                'getProductEnquiry',
            ])
            ->find($id);

        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new TransporterEnquiryDetailResource($enquiry),
        ]);
    }

    public function biddingList(Request $request, int $id): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $enquiry = TransporterProductEnquiry::where('user_id', $ownerId)->find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        $bids = TransporterProductEnquiry::where('product_enquiries_id', $enquiry->product_enquiries_id)
            ->where('user_id', '!=', $ownerId)
            ->where('status', '!=', 'pending')
            ->orderBy('price', 'asc')
            ->get(['id', 'price', 'status', 'is_mark']);

        return response()->json([
            'success' => true,
            'data'    => $bids->map(fn ($b) => [
                'id'        => $b->id,
                'price'     => $b->price,
                'status'    => $b->status,
                'is_marked' => (bool) $b->is_mark,
            ])->all(),
        ]);
    }

    public function respond(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'note'  => ['nullable', 'string', 'max:500'],
        ]);

        $ownerId = $this->ownerId($request);
        $enquiry = TransporterProductEnquiry::where('user_id', $ownerId)->find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }
        if ($enquiry->is_mark) {
            return response()->json(['success' => false, 'message' => 'This enquiry has been awarded and cannot be updated.'], 422);
        }

        $enquiry->price  = $data['price'];
        $enquiry->status = 'replied';
        $history = is_array($enquiry->history) ? $enquiry->history : [];
        $history[] = [
            'status'     => 'replied',
            'note'       => $data['note'] ?? null,
            'by'         => $request->user()->id,
            'created_at' => Carbon::now()->toIso8601String(),
        ];
        $enquiry->history = $history;
        $enquiry->save();

        return response()->json([
            'success' => true,
            'message' => 'Quotation submitted successfully.',
            'data'    => new TransporterEnquiryDetailResource(
                $enquiry->fresh(['getBrand', 'getCommodityProduct.getCategory', 'getProductEnquiry'])
            ),
        ]);
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
