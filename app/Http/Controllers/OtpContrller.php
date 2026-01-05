<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OtpContrller extends Controller
{
public function check(Request $request){
 $validated=$request->validate([
     'otp_code'=>['required','string'],
 ]);

 $user=auth()->user();
 if($validated['otp_code']==$user->otp_code){
     $user->otp_code=null;
     $user->otp_expired_at=null;
     $user->save();
     return response()->json([
         'message'=>'OTP Verified Successfully',
     ]);
 }

 return response()->json([
     'message'=>'uncorrect OTP Code',
 ]) ;

}



}
