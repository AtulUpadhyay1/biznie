<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct() {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('admin.auth.login',['page_title' => 'Admin Login']);
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required|min:8'
        ]);

        // Attempt to log the user in
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember))
        {
            // if successful, then redirect to their intended location
            return redirect()->route('admin.dashboard')->with('success', 'Signed in successfully !!');
        }
        // if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(['password' => ['These credentials don\'t match our records.','Or Incorrect Password']]);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'Signed Out successfully !!');
    }
}
