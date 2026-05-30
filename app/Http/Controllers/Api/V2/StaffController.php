<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\StaffResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $owner = $this->ownerOrFail($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = User::where('added_by', $owner->id)
            ->where('is_staff', 1)
            ->when($request->search, fn ($q) => $q->where(function ($w) use ($request) {
                $term = '%' . $request->search . '%';
                $w->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('role', 'like', $term);
            }))
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => StaffResource::collection($list)->resolve(),
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
        $owner = $this->ownerOrFail($request);
        $staff = User::where('added_by', $owner->id)->where('is_staff', 1)->find($id);
        if (! $staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found.'], 404);
        }
        return response()->json(['success' => true, 'data' => new StaffResource($staff)]);
    }

    public function store(Request $request): JsonResponse
    {
        $owner = $this->ownerOrFail($request);
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone'      => ['required', 'string', 'max:20', Rule::unique('users', 'phone')],
            'role'       => ['required', 'string', 'max:255'],
            'permission' => ['required', 'array'],
            'password'   => ['nullable', 'string', 'min:6'],
        ]);

        $staff = new User();
        $staff->name       = $data['name'];
        $staff->email      = $data['email'];
        $staff->phone      = $data['phone'];
        $staff->password   = bcrypt($data['password'] ?? $data['phone']);
        $staff->type       = $owner->type;
        $staff->is_staff   = 1;
        $staff->added_by   = $owner->id;
        $staff->role       = $data['role'];
        $staff->permission = $data['permission'];
        $staff->save();

        return response()->json([
            'success' => true,
            'message' => 'Staff created successfully.',
            'data'    => new StaffResource($staff),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $owner = $this->ownerOrFail($request);
        $staff = User::where('added_by', $owner->id)->where('is_staff', 1)->find($id);
        if (! $staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found.'], 404);
        }

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id)],
            'phone'      => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($staff->id)],
            'role'       => ['required', 'string', 'max:255'],
            'permission' => ['required', 'array'],
            'password'   => ['nullable', 'string', 'min:6'],
        ]);

        $staff->name       = $data['name'];
        $staff->email      = $data['email'];
        $staff->phone      = $data['phone'];
        $staff->role       = $data['role'];
        $staff->permission = $data['permission'];
        if (! empty($data['password'])) {
            $staff->password = bcrypt($data['password']);
        }
        $staff->save();

        return response()->json([
            'success' => true,
            'message' => 'Staff updated successfully.',
            'data'    => new StaffResource($staff),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $owner = $this->ownerOrFail($request);
        $staff = User::where('added_by', $owner->id)->where('is_staff', 1)->find($id);
        if (! $staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found.'], 404);
        }
        $staff->delete();
        return response()->json(['success' => true, 'message' => 'Staff deleted successfully.']);
    }

    public function permissionTemplate(Request $request): JsonResponse
    {
        $owner = $this->ownerOrFail($request);
        $template = $owner->type === 'customer' ? customerPermissionList() : permissionList();

        return response()->json([
            'success' => true,
            'data'    => [
                'type'     => $owner->type,
                'template' => $template,
            ],
        ]);
    }

    /**
     * Only the account owner (non-staff) can manage staff.
     */
    private function ownerOrFail(Request $request): User
    {
        $user = $request->user();
        if ($user->is_staff) {
            throw new AuthorizationException('Only the account owner can manage staff.');
        }
        return $user;
    }
}
