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
    $user['avatar_path']=$user->avatar_path ?url('storage/'.$user->avatar_path) :null;
    $user['id_card_path']=$user->id_card_path ? url('storage/'.$user->id_card_path) :null;
return response()->json([
    'message' => 'User approved successfully'],
     200);
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
public function showUsers(){
    $admin=auth()->user();
    $users = User::where('id', '!=', $admin->id)->get();
    foreach ($users as $user) {
        $user['avatar_path']=$user->avatar_path ?url('storage/'.$user->avatar_path) :null;
        $user['id_card_path']=$user->id_card_path ? url('storage/'.$user->id_card_path) :null;
    }
    return response()->json($users,200);
}
}
