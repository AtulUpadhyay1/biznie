<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $list = User::where('added_by', auth()->id())
            ->latest()
            ->where('is_staff', 1)
            ->when($request->search, function($query) use ($request){
                $query->search($request->search);
            })
            ->paginate(10);

        return response([
            'success'   => true,
            'message'   => 'Staff List.',
            'data'      => $list
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'required|numeric|unique:users,phone',
            'role'      => 'required|string|max:255',
            'permission'=> 'required|array',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->phone);
        $user->type = auth()->user()->type;
        $user->is_staff = 1;
        $user->added_by = auth()->id();
        $user->role = $request->role;
        $user->permission = $request->permission ? $request->permission : [];
        $user->save();

        return response([
            'success'   => true,
            'message'   => 'Staff created successfully.',
            'data'      => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::where('added_by', auth()->id())->where('is_staff', 1)->find($id);
        if(!$user){
            return response([
                'success'   => false,
                'message'   => 'Staff not found.'
            ], 404);
        }

        return response([
            'success'   => true,
            'message'   => 'Staff details.',
            'data'      => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,'.$id,
            'phone'     => 'required|numeric|unique:users,phone,'.$id,
            'role'      => 'required|string|max:255',
            'permission'=> 'required|array',
        ]);

        $user = User::where('added_by', auth()->id())->where('is_staff', 1)->find($id);
        if(!$user){
            return response([
                'success'   => false,
                'message'   => 'Staff not found.'
            ], 404);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if($request->password){
            $user->password = bcrypt($request->password);
        }
        $user->role = $request->role;
        $user->permission = $request->permission ? $request->permission : [];
        $user->save();

        return response([
            'success'   => true,
            'message'   => 'Staff updated successfully.',
            'data'      => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('added_by', auth()->id())->where('is_staff', 1)->find($id);
        if(!$user){
            return response([
                'success'   => false,
                'message'   => 'Staff not found.'
            ], 404);
        }
        $user->delete();

        return response([
            'success'   => true,
            'message'   => 'Staff deleted successfully.'
        ], 200);
    }
}
