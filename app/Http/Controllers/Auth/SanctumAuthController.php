<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VendorLoginRequest;
use App\Http\Requests\Auth\VendorRegisterRequest;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\VendorOtpMail;



class SanctumAuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Sanctum Authentication Controller
    | This controller handles authenticating users and vendor via sanctum and
    | redirecting them to your home screen. 
    |
    */

    /**
    * Vendor login method
    * @param VendorLoginRequest $request
    * @return \Illuminate\Http\JsonResponse
    * @exception ValidationException
    */

    public function vendorLogin(VendorLoginRequest $request)
    {
        $vendor = Vendor::where('email', $request->validated('email'))->first();
        if(!$vendor){
            throw ValidationException::withMessages([
                'email' => ['This email address is not registered with ' . env('APP_NAME')],
            ]);
        }
       $deviceAlreadyExists = $vendor->tokens->contains('name', $request->device_name);
        if (!$deviceAlreadyExists) {
            Log::info('A new device has been logged in. Device: ' . $request->device_name);
            // send main
        }
        

        return response()->json([
            'token' => $vendor->createToken($request->validated('device_name'), ['vendor'])->plainTextToken,
            'logged_in' => true,
            'user' => $vendor
        ]);
    }

    /**
    * Vendor register method
    * @param VendorRegisterRequest $request
    * @return \Illuminate\Http\JsonResponse
    * @exception ValidationException
    */

    public function vendorRegister(VendorRegisterRequest $request) 
    {
        $vendor = Vendor::where('email', $request->input('email'))->first();
        if($vendor === null){
            $vendor = Vendor::create([
                'email' => $request->validated('email'),
                'otp' => rand(100000, 999999),
                'is_verified' => false
            ]);
        }else {
            $deviceAlreadyExists = $vendor->tokens->contains('name', $request->header('User-Agent'));
            if (!$deviceAlreadyExists) {
                Log::info('A new device has been logged in. Device: ' . $request->device_name);
                // send main
            }
        }
    //    if (Validator::make(['email' => $vendor->email], ['email' => 'required||email:rfc,dns'])->passes()) {
    //         Mail::to($vendor->email)->send(new VendorOtpMail($vendor->otp));
    //     }

        return response()->json([
            'token' => $vendor->createToken($request->header('User-Agent'), ['vendor'])->plainTextToken,
            'logged_in' => true,
            'user' => [
                'name' => $vendor->name,
                'email' => $vendor->email,
                'is_verified' => $vendor->is_verified,
            ]
        ]);
    }

    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:vendors,email',
        ]);
        $vendor = Vendor::where('email', $validated['email'])->firstOrFail();
        $vendor->update([
            'otp' => rand(100000, 999999),
        ]);

        return response()->json([
            'message' => 'OTP Sent to you email'
        ]);
    }

    public function otpVerification(Request $request)
    {
        $validated = $request->validate([
            'otp'   => 'required|digits:6|exists:vendors,otp',
            'email' => 'required|email|exists:vendors,email',
        ]);
    
        $vendor = Vendor::where('email', $validated['email'])
            ->where('otp', $validated['otp'])
            ->firstOrFail();
    
        $vendor->update([
            'otp' => null,
            'is_verified' => true,
            'email_verified_at' => now()
        ]);
        
        return response()->json([
            'message' => 'OTP verified successfully',
            'user'  =>  [
                'name' => $vendor->name,
                'email' => $vendor->email,
                'is_verified' => $vendor->is_verified,
            ],
        ]);
    }

}
