<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Services\FcmService;
use App\Http\Resources\Auth\RegisterResource;
use App\Http\Resources\Auth\UpdateUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
public function register(RegisterRequest $request){
    $validated = $request->validated();
    if ($request->hasFile('avatar_path')) {
        $validated['avatar_path'] = $request->file('avatar_path')->store('avatars', 'public');
    }

    if ($request->hasFile('id_card_path')) {
        $validated['id_card_path'] = $request->file('id_card_path')->store('id_cards', 'public');
    }
    $validated['password'] = Hash::make($validated['password']);
$user=User::create($validated);
//$user->password=Hash::make($validated['password']);
//
//    $user->save();
$user_token=auth()->login($user);
return response()->json([
    'message' => 'wait until admin approve your account',
//    new RegisterResource($user)
//    'token'=>$user_token
//    ,'expires_in'=>auth()->factory()->getTTL() * 60
]);

}
///////////////////andrew was here///////////////////////////
public function login(LoginRequest $request){
    $validated = $request->validated();
 $user=User::where('phone',$validated['phone'])->first();
  if(!$user) {
      return response()->json([
          'message' => 'please enter valid phone number'
      ], 404);
  }

          if (!Hash::check($validated['password'], $user->password)) {
              return response()->json([
                  'message' => 'invalid password'
              ], 401);

          }
          if ($user->is_admin) {
              $token=auth()->login($user);
              return response()->json(['user' => new LoginResource($user),
                  'token'=>$token,
                  'expires_in' => auth()->factory()->getTTL() * 60

              ]);
          }
    if($user->status==='pending') {
        return response()->json([
            'message' => 'Your account is pending until approval'
        ]);
    }
    if($user->status==='rejected') {
        return response()->json([
            'message' => 'Your account is rejected by admin'
        ],403);
    }

          $user->generate_otp_code();
    $otp=$user->otp_code;
    app(\App\Services\UltraMsgService::class)->sendOtp($user->phone, $otp);


    return response()->json([
    'message' => 'please check your whatsapp number '
]);





      }

////////////////////////////////////
public function logout(){
    auth()->logout();
    return response()->json([
        'message' => 'logout success'
    ]);
}
/////////////////////////////andrew was here//////////////////////////////////
public function refresh(){
return response()->json(['token'=>auth()->refresh(), 'expires_in'=>auth()->factory()->getTTL() * 60
],200);
}
public function me(){
$user = auth()->user();
$user['avatar_path']=$user->avatar_path ?url('storage/'.$user->avatar_path) :null;
$user['id_card_path']=$user->id_card_path ? url('storage/'.$user->id_card_path) :null;
    return response()->json($user,200);//show me curent user informations//andrew//

}
public function update(UpdateUserRequest $request){
    $validated = $request->validated();

    $id=auth()->id();
    $user=User::find($id);
    if(!$user) {
        return response()->json([
            'message' => 'user not found'
        ]);
    }
    if ($request->hasFile('avatar_path')) {
        $validated['avatar_path']=$request->file('avatar_path')->store('avatars', 'public');
    }
    if($request->hasFile('id_card_path')){
        $validated['id_card_path'] = $request->file('id_card_path')->store('id_cards', 'public');
    }
    $validated['phone']=$user['phone'];
    $user->update($validated);
    return response()->json([
        'message' => 'user updated successfully',
        'user'=>new UpdateUserResource($user)

    ]);
}

public function forgetPassword(Request $request){
    $validate=$request->validate(['phone'=>'required','string']);
    $user=User::where('phone',$validate['phone'])->first();
    $user->otp_code=null;
    $user->otp_expired_at=null;
    $user->otp_attempts=0;
    $user->save();

    if(!$user) {
        return response()->json([
            'message' => 'please enter valid phone number'
        ]);
    }
    $user->generate_otp_code();
    $otp=$user->otp_code;
    app(\App\Services\UltraMsgService::class)->sendOtp($user->phone, $otp);
    return response()->json([
        'message' => 'please check your whatsapp number '
    ]);


}

public function resetPassword(Request $request){
    $validate=$request->validate([
        'otp_code'=>'required',
        'phone'=>'required','string',
        'password'=>'required','string','confirmed'
    ]);
    $user=User::where('phone',$validate['phone'])->first();

    if(!$user) {
        return response()->json([
            'message' => 'please enter valid phone number'
        ],404);
    }

    if($user->otp_expired_at<now()->addMinutes(60)){
        return response()->json(['message'=>'The code has expired'],429);
    }
    if($user->otp_attempts>=3){
        $user->otp_attempts=0;
        $user->otp_code=null;
        $user->otp_expired_at=null;
        $user->save();
        return response()->json(['message'=>'You have exceeded your number of attempts.'],429);
    }
    if ($validate['otp_code']!=$user->otp_code){
        $user->otp_attempts=$user->otp_attempts+1;
        $user->save();
        return response()->json(['message'=>'OTP code not matched'],404);
    }


    $user->update([
        'password' => Hash::make($validate['password']),
        'otp_code'=>null,
        'otp_expired_at'=>null,
        'otp_attempts'=>0
    ]);

return response()->json([
    'message' => 'password updated successfully'
],200);
}


}





