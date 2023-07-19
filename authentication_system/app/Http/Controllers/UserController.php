<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use Exception;
use App\Models\User;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    // User Registration
    public function UserRegistration(Request $request){
        try{
            User::create([
                'f_Name' => $request->input('firstName'),
                'l_Name' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password')
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'User Registration Successful.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => 'Fail.',
                'message' => 'Something went wrong.'
            ]);
        }
    }

    // User Login
    public function UserLogin(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');
        $count = User::where('email', '=', $email)
        ->where('password', '=', $password)
        ->count();

        if($count==1){
            $token = JWTToken::CreateJWTToken($request->input('email'));

            return response()->json([
                'status' => 'success',
                'message' => 'Login Successful.',
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized.'
            ]);
        }
    }

    // Send OTP token to email
    public function SendOTPToken(Request $request){
        $email = $request->input('email');
        $otp = rand(100000, 900000);
        $count = User::where('email', '=', $email)->count();

        if($count == 1){
            Mail::to($email)->send(new OTPMail($otp));
            // Update Db Otp
            User::where('email', '=', $email)->update(['otp'=>$otp]);

            return response()->json([
                'status' => 'success',
                'message' => '6 digits otp has sent to your email.'
            ], 200);
        } else{
            return response()->json([
                'status' => 'fail.',
                'message' => 'Unauthorized.'
            ]);
        }
    }

    // Verify OTP Token for reset password
    public function VerifyOTPToken(Request $request){
        $email = $request->input('email');
        $otp = $request->input('otp');

        $count = User::where('email', '=', $email)
        ->where('otp', '=', $otp)->count();

        if($count ==1){
            // Update DB otp
            User::where('email', '=', $email)->update(['otp'=> '0']);

            $token = JWTToken::CreateTokenForResetPass($request->input('email'));

            return response()->json([
                'status' => 'success',
                'message' => 'OTP Verified Successfully.',
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail.',
                'message' => 'Unauthorized.'
            ]);
        }
    }
}
