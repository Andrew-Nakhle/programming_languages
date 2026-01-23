<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Flat\FlatRatingRequest;
use App\Models\Flat;
use App\Models\flatRating;
use Illuminate\Http\Request;

class FlatRatingController extends Controller
{
    public function createFlatRating($flatId, FlatRatingRequest $request)
    {
        $validated = $request->validated();

        $flat = Flat::find($flatId);
        if (!$flat) {
            return response()->json([
                'message' => 'Flat not found wrong id',
            ], 404);
        }

        $rate = FlatRating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'flat_id' => $flatId,
            ],
            [
                'rating' => $validated['rating'],
            ]
        );

        return response()->json([
            'message' => 'Flat rated successfully',
            'rating'  => $rate
        ]);
    }

    public function avgRating($flatId){
$flat=Flat::with('ratings')->find($flatId);

        if(!$flat){
            return response()->json([
                'message'=>'flat not found wrong id',
            ]);
        }
$avg=$flat->ratings->avg('rating');
return response()->json([
    'avg'=>$avg
]);
    }
}
