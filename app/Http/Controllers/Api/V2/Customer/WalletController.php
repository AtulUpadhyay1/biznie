<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\WalletTransactionResource;
use App\Models\CashWalletTransaction;
use App\Models\CreditWalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $ownerId = $user->is_staff ? (int) $user->added_by : (int) $user->id;

        return response()->json([
            'success' => true,
            'data'    => [
                'cash_balance'          => (float) ($user->cash_balance ?? 0),
                'credit_balance'        => (float) ($user->credit_balance ?? 0),
                'assign_credit_balance' => (float) ($user->assign_credit_balance ?? 0),
                'recent_cash'           => WalletTransactionResource::collection(
                    CashWalletTransaction::where('user_id', $ownerId)->latest()->limit(5)->get()
                )->resolve(),
                'recent_credit'         => WalletTransactionResource::collection(
                    CreditWalletTransaction::where('user_id', $ownerId)->latest()->limit(5)->get()
                )->resolve(),
            ],
        ]);
    }

    public function cash(Request $request): JsonResponse
    {
        return $this->paginated($request, CashWalletTransaction::class);
    }

    public function credit(Request $request): JsonResponse
    {
        return $this->paginated($request, CreditWalletTransaction::class);
    }

    private function paginated(Request $request, string $model): JsonResponse
    {
        $user = $request->user();
        $ownerId = $user->is_staff ? (int) $user->added_by : (int) $user->id;
        $perPage = min((int) $request->get('per_page', 20), 50);

        $list = $model::where('user_id', $ownerId)->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => WalletTransactionResource::collection($list)->resolve(),
            'meta'    => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
        ]);
    }
}
