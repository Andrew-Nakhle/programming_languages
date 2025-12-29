<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
public function approveUser($id){
    $admin=auth()->user();
    if($admin->id==$id){
     return response()->json([
         'message'=>'You cannot approve your own account',
     ],403)  ;
    }
    $user=User::find($id);

if(!$user){
    return response()->json(['error' => 'User not found'], 404);
}
$user->status='approved';
$user->save();
return response()->json([
    'message' => 'User approved successfully',
    'user'=>$user], 200);
}
//////////////////////////////andrew was here//////////////////////////////////
public function rejectUser($id){
    $admin=auth()->user();
    if($admin->id==$id){
        return response()->json([
            'message'=>'You cannot reject your own account',
        ],403)  ;
    }
    $user=User::find($id);
    if(!$user){
        return response()->json(['error' => 'User not found'], 404);
    }
    $user->status='rejected';
    $user->save();
    return response()->json(['message' => 'User rejected successfully'], 200);
}

}
