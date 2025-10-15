<?php

namespace App\Http\Controllers\Api\Auth;

use Mail;
use App\Models\User;
use App\Mail\EmailOtp;
use App\Models\UserOtp;
use App\Models\TempUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserPromotionHistory;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LoginResource;
use Illuminate\Validation\Rules\Password;
use App\Http\Resources\Seller\BusinessResource;
use App\Http\Resources\Seller\KycDetailResource;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'phone'     => 'required|numeric|digits:10|unique:users,phone',
            'email'     => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()]
        ]);
        $temp_user = TempUser::where('phone', $request->phone)->first();
        if(!$temp_user){
            $temp_user = new TempUser;
        }
        $temp_user->name = $request->name;
        $temp_user->type = 'customer';
        $temp_user->email = $request->email;
        $temp_user->phone = $request->phone;
        $temp_user->password = bcrypt($request->password);
        $temp_user->save();

        sendOtp($request->phone);

        return response([
            'success'   => true,
            'message'   => 'Otp send successfully.'
        ],200);

    }

    public function otpLogin(Request $request)
    {
        $this->validate($request, [
            'phone'     => 'required|numeric|digits:10',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if(!$user){
            $temp_user = TempUser::where('phone', $request->phone)->first();
            if(!$temp_user){
                $temp_user = new TempUser;
            }
            $temp_user->name = 'User';
            $temp_user->type = 'customer';
            $temp_user->email = NULL;
            $temp_user->phone = $request->phone;
            $temp_user->password = NULL;
            $temp_user->save();
        }

        if($user && $user->status == 'in_active'){
            return response([
                'success'   => false,
                'message'   => 'Your account has been deactivated.',
            ],400);
        }

        sendOtp($request->phone);
        // sendMySmsShopMessage('hAoIyvpY6HUvbZdX', 'ABEERH', $request->phone, 'Hello, Your OTP to reset password is '.rand(1111, 9999).' ABHEERH');


        return response([
            'success'   => true,
            'message'   => 'Otp send successfully.'
        ],200);

    }

    public function emailLogin(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials)){
            return response([
                'success'   => false,
                'message'   => 'Invalid credentials.',
            ],400);
        }

        $user = Auth::user();

        if($user->status != 'active'){
            return response([
                'success'   => false,
                'message'=> 'Your account has been deactivated.',
            ],400);
        }

        return response([
            'success'   => true,
            'token'     => $user->createToken('auth_token')->plainTextToken,
            'message'   => 'You are successfully login.',
            'data'      => new LoginResource(auth()->user()),
        ],200);

    }

    public function verifyOtp(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|numeric|digits:10',
            'otp' => 'required|numeric',
        ]);

        $user = User::where('phone', $request->phone)->first();
        $checkOtp = UserOtp::where('phone', $request->phone)->first();
        if(!$checkOtp){
            return response([
                'success'   => false,
                'message'   => 'Otp not sent on this number.',
            ],400);
        }

        if($checkOtp->otp != $request->otp && $request->otp != websiteSetupValue('master_otp')){
            return response([
                'success'   => false,
                'message'   => 'Invalid otp entered.',
            ],400);
        }

        if($user){

            if($user->status != 'active'){
                return response([
                    'success'   => false,
                    'message'=> 'Your account has been deactivated.',
                ],400);
            }

            $checkOtp->delete();

            Auth::login($user);
            return response([
                'success'   => true,
                'token'     => $user->createToken('auth_token')->plainTextToken,
                'message'   => 'You are successfully login.',
                'data'      => new LoginResource(auth()->user()),
            ],200);

        }else{

            $temp_user = TempUser::where('phone', $request->phone)->first();
            if($temp_user){

                $checkOtp->delete();

                $user = new User;
                $user->name = $temp_user->name;
                $user->type = "customer";
                $user->email = $temp_user->email;
                $user->phone = $temp_user->phone;
                $user->phone_verified_at = date('Y-m-d H:i:s');
                $user->password = $temp_user->password;
                $user->save();

                $user_log_history = new UserPromotionHistory;
                $user_log_history->user_id = $user->id;
                $user_log_history->new_type = "customer";
                $user_log_history->save();

                Auth::login($user);

                $temp_user->delete();

                return response([
                    'success'   => true,
                    'token'     => $user->createToken('auth_token')->plainTextToken,
                    'message'   => 'You are successfully register.',
                    'data'      => new LoginResource(auth()->user()),
                ],200);

            }

            return response([
                'success'   => false,
                'message'   => 'Phone not found.',
            ],400);
        }
    }

    public function businessInterest(Request $request)
    {
        $this->validate($request, [
            'business_interest'   => 'required|array'
        ]);
        $user = auth()->user();
        $user->business_interest = $request->business_interest;
        $user->save();
        return response([
            'success'   => true,
            'message'   => 'Interest business category updated successfully.',
        ],200);
    }

    public function userDetail()
    {
        $user = auth()->user();
        return response([
            'success'   => true,
            'data'      => [
                'name'      => $user->name,
                'type'      => $user->type,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'phone_verified_at' => dateTimeFormat($user->phone_verified_at),
                'business_details'  => $user->getBusiness ? new BusinessResource($user->getBusiness) : null,
                'kyc_details'       => $user->getSellerKycDetail ? new KycDetailResource($user->getSellerKycDetail) : null,
            ],
        ],200);
    }

    public function emailOtp(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response([
                'success'   => false,
                'message'   => 'Email not found.',
            ],400);
        }

        if($user && $user->status == 'in_active'){
            return response([
                'success'   => false,
                'message'   => 'Your account has been deactivated.',
            ],400);
        }

        $data = UserOtp::where('email', $request->email)->first();
        if(!$data){
            $data = new UserOtp;
        }
        $otp = rand(1111, 9999);
        $data->otp = $otp;
        $data->email = $request->email;
        $data->save();

        Mail::to($request->email)->send(new EmailOtp(
            otp: $otp,
            userName: $user->name,
            userEmail: $user->email,
            expiryMinutes: 15
        ));

        return response([
            'success'   => true,
            'message'   => 'Otp send successfully.'
        ],200);
    }

    public function verifyEmailOtp(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->first();
        $checkOtp = UserOtp::where('email', $request->email)->first();
        if(!$checkOtp){
            return response([
                'success'   => false,
                'message'   => 'Otp not sent on this email.',
            ],400);
        }

        if($checkOtp->otp != $request->otp && $request->otp != websiteSetupValue('master_otp')){
            return response([
                'success'   => false,
                'message'   => 'Invalid otp entered.',
            ],400);
        }

        if($user){

            if($user->status != 'active'){
                return response([
                    'success'   => false,
                    'message'=> 'Your account has been deactivated.',
                ],400);
            }

            $checkOtp->delete();

            Auth::login($user);
            return response([
                'success'   => true,
                'token'     => $user->createToken('auth_token')->plainTextToken,
                'message'   => 'You are successfully login.',
                'data'      => new LoginResource(auth()->user()),
            ],200);

        }else{

            return response([
                'success'   => false,
                'message'   => 'Email not found.',
            ],400);
        }
    }

    public function deleteAccount(Request $request)
    {
        // $this->validate($request, [
        //     'password'  => 'required',
        // ]);
        try {
            $user = auth()->user();

            // if(!\Hash::check($request->password, $user->password)){
            //     return response([
            //         'success'   => false,
            //         'message'   => 'Invalid password entered.',
            //     ],400);
            // }

            Auth::logout();

            // Delete user related data if any

            $user->delete();

            return response([
                'success'   => true,
                'message'   => 'Your account has been deleted successfully.',
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong while deleting your account.',
                'error'     => $th->getMessage(),
            ],500);
        }
    }
}
