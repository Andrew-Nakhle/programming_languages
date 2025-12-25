<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\LoginResource;

use App\Http\Resources\RegisterResource;
use App\Http\Resources\UpdateUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
$user=User::create($validated,201);
//$user->password=Hash::make($validated['password']);
//
//    $user->save();
$user_token=auth()->login($user);
return response()->json([new RegisterResource($user),'token'=>$user_token
    ,'expires_in'=>auth()->factory()->getTTL() * 60
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
  if(!Hash::check($validated['password'],$user->password)){
      return response()->json([
          'message' => 'invalid password'
      ], 401);

  }
  $token=auth()->login($user);
  return response()->json([new LoginResource($user),
      'token'=>$token, 'expires_in'=>auth()->factory()->getTTL() * 60
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
]);
}
public function me(){
$user = auth()->user();
$user['avatar_path']=$user->avatar_path ?asset('storage/'.$user->avatar_path) :null;
$user['id_card_path']=$user->id_card_path ? asset('storage/'.$user->id_card_path) :null;
    return response()->json($user);//show me curent user informations//andrew//

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
}
