<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\LoginResource;
use App\Models\User;
use Illuminate\Http\Request;

class OtpController extends Controller
{
public function check(Request $request){
 $validated=$request->validate([
     'otp_code'=>['required','string'],
     'phone'=>['required','string'],
 ]);
$user=User::where('phone',$validated['phone'])->first();
if(!$user){
    return response()->json(['message'=>'Phone number not found'],404);
}
    if($user->otp_expired_at<now()->addMinutes(60)){
        return response()->json([
            'message'=>'The code has expired',
        ]);
    }

if($user->otp_attempts>=3){
    $user->otp_attempts=0;
    $user->otp_code=null;
    $user->otp_expired_at=null;
    $user->save();
return response()->json(['message'=>'You have exceeded your number of attempts.'],429);
}

if ($validated['otp_code']!=$user->otp_code){
$user->otp_attempts=$user->otp_attempts+1;
$user->save();
    return response()->json(['message'=>'OTP code not matched'],404);
}


    $user->otp_attempts=0;
     $user->otp_code=null;
     $user->otp_expired_at=null;
     $user->save();
     $token = auth()->login($user);
     return response()->json(['user'=>new LoginResource($user),
         'token' => $token, 'expires_in' => auth()->factory()->getTTL() * 60
     ]);



}

public function verifyOtp(Request $request){
    $validated=$request->validate([
        'otp_code'=>['required','string'],
        'phone'=>['required','string'],
    ]);
    $user=User::where('phone',$validated['phone'])->first();
    if(!$user){
        return response()->json(['message'=>'Phone number not found'],404);
    }
    if($user->otp_expired_at<now()->addMinutes(60)){
        return response()->json(['message'=>'The code has expired'],429);
    }
    if($user->otp_attempts>=3){
        return response()->json(['message'=>'You have exceeded your number of attempts.'],429);
    }
    if ($validated['otp_code']!=$user->otp_code){
        $user->otp_attempts=$user->otp_attempts+1;
        $user->save();
        return response()->json(['message'=>'OTP code not matched'],404);
    }

    return response()->json(['message'=>'please enter the new password'],200);
}



}
