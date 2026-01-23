<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Favorits;
use App\Http\Controllers\Controller;
use App\Models\Flat;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function addToFavorite($flatId)
    {
        $flat = Flat::with('favorites')->find($flatId);
        $user = auth()->user();
        if (!$flat) {
            return response()->json([
                'message' => 'flat not found'
            ], 404);
        }
        $exists = Favorite::where('user_id', $user->id)->where('flat_id', $flatId)->exists();
        if ($exists) {
            return response()->json([
                'message' => 'already favorited'
            ], 409);
        }
        $favorite = Favorite::create([
            'flat_id' => $flatId,
            'user_id' => $user->id
        ]);
        return response()->json([
            'message' => 'added to favorite',
            'id' => $favorite->id
        ], 201);
    }

    public function removeFromFavorite($favoriteId)
    {
        $favorite = Favorite::find($favoriteId);
        $user = auth()->user();
        if (!$favorite) {
            return response()->json([
                'message' => 'favorite not found'
            ]);
        }
        $favorite->delete();


        return response()->json([
            'message' => 'removed from favorite',
        ], 200);

    }

    public function showFavorites()
    {
        $user=auth()->user();
        $user=User::with('favorites')->find($user->id);
        $favorites=$user->favorites;
        return response()->json($favorites);

    }
}
