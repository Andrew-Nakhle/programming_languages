<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Flat\CreateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Flat\SearchRequest;
use App\Http\Requests\Flat\UpdateRequest;
use App\Http\Resources\Flat\FlatResource;
use App\Models\Flat;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class FlatController extends Controller
{
    public function createFlat(CreateRequest $request){
        $request->merge([ 'section' => strtolower($request->section) ]);//هاد التابغ يا ابو مجد عم الكل اذا بدي عدل البيانات يلي جاي من request قبل ما اعملها validation يا كبيررررر
        $validated=$request->validated();
        if ($request->hasFile('flat_image')) {
            $validated['flat_image'] = $request->file('flat_image')->store('flats_images', 'public');
        }

        if (Flat::where('governorate', $validated['governorate'])
            ->where('city', $validated['city'])
            ->where('address', $validated['address'])
            ->where('section', $validated['section'])
            ->where('price', $validated['price'])
            ->where('space', $validated['space'])
            ->where('has_elevator', $validated['has_elevator'])
            ->where('rooms', $validated['rooms'])
            ->where('floor', $validated['floor'])
            ->where('is_furnished', $validated['is_furnished'])
            ->where('available_date', $validated['available_date'])
            ->where('flat_image', $validated['flat_image'])
            ->exists()

        ){
            return response()->json([
                'message' => 'this flat  already exists'
            ]);
        }

$validated['user_id']=auth()->id();
       $flat=Flat::create($validated);
       return response()->json(new FlatResource($flat),201);

    }
    //////////////////////////andrew was here///////////////////////////
    public function showFlatsByUserId(){
        $user_id=auth()->id();
        $flats = Flat::where('user_id', $user_id)->get();
        if($flats->isEmpty()){
            return response()->json([
                'message'=>'There are no flats created yet by this user',
            ]);
        }
        return response()->json(FlatResource::collection($flats),200);
        //في طريقة تانية بس انا ماكنت عامل الشو اسمو العلاقات وقتها بتعمل هيك
        //$user=User::with('flats')->find($user_id);
        //$flats=$user->flats();
    }
    //////////////////////////andrew was here/////////////////////////////////
    public function showFlatsById($id){
        $flat =Flat::find($id);
        if(!$flat){
            return response()->json([
                'message'=>'this flat does not exist'
            ]);
        }
return response()->json(new FlatResource($flat));
    }
    //////////////////////////andrew was here/////////////////////////////
    public function deleteFlat($id)
    {
        $flat = Flat::find($id);
$user_id = auth()->id();
        if (!$flat) {
            return response()->json(['message' => 'Flat not found'], 404);
        }
if ($flat->user_id === $user_id) {
        $flat->delete();

        return response()->json(['message' => 'Flat deleted successfully'], 200);
    }
return response()->json(['message' => 'You do not have the authority to delete this flat'], 403);
    }

    ///////////////////////andrew was here////////////////////////////
    public function searchFlats(SearchRequest $request){
        $request->merge([ 'section' => strtolower($request->section) ]);
        $validated=$request->validated();
            $query=Flat::query();

if($validated['no_filter']){
    $flats=Flat::get();
    if ($flats->isEmpty()) {
        return response()->json([
            'message'=>'There are no flats created yet',
        ]);
        return response()->json(FlatResource::collection($flats),200);
    }
}

        if (!empty($validated['city'])) {
            $query->where('city', $validated['city']);
        }
        if (!empty($validated['rooms'])) {
            $query->where('rooms', $validated['rooms']);
        }
        if (!empty($validated['section'])) {
            $query->where('section', $validated['section']);
        }
        if (!empty($validated['address'])) {
            $query->where('address', $validated['address']);
        }
        if (!empty($validated['price'])) {
            $query->where('price', '<=', (float)$validated['price']);
        }
        if (!empty($validated['floor'])) {
            $query->where('floor', $validated['floor']);
        }
        if (!empty($validated['space'])) {
            $query->where('space', $validated['space']);
        }
        if (isset($validated['has_elevator'])) {
            $query->where('has_elevator', $validated['has_elevator']);
        }
        if (isset($validated['is_furnished'])) {
            $query->where('is_furnished', $validated['is_furnished']);
        }

        $flats = $query->get();
        if ($flats->isEmpty()) {
            return response()->json(['message' => 'Flat not found'], 404);
        }
        return response()->json(FlatResource::collection($flats),200);

    }
///////////////////////////////abumajd was here////////////////////////////////
public function updateFlat($id,UpdateRequest $request){
    $request->merge([ 'section' => strtolower($request->section) ]);

    $validated=$request->validated();

        $flat=Flat::find($id);
        $user_id = auth()->id();
    if ($request->hasFile('flat_image')) {
        $validated['flat_image'] = $request->file('flat_image')->store('flats_images', 'public');
    }
        if (!$flat) {
            return response()->json(['message' => 'Flat not found you cant update it'], 404);
        }
        if ($flat->user_id === $user_id) {
            $flat->update($validated);
           return response()->json(['flat'=>new FlatResource($flat),'status'=>$validated['status']],201);
        }
        return response()->json(['message' => 'You do not have the authority to update this flat'], 403);
}

}
