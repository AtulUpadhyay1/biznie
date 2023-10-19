<?php

namespace App\Http\Controllers\Seller\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('seller.auth.login',['page_title' => 'Seller Login']);
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required|min:8'
        ]);
        // Attempt to log the user in
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember))
        {
            if(auth()->user()->type == 'seller'){
                // if successful, then redirect to their intended location
                return redirect()->route('seller.dashboard')->with('success', 'Signed in successfully !!');
            }
            Auth::logout();
            return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(['password' => ['These credentials don\'t match our records.','Or Incorrect Password']]);
        }
        // if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(['password' => ['These credentials don\'t match our records.','Or Incorrect Password']]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('seller.login')->with('success', 'Signed Out successfully !!');
    }
}
