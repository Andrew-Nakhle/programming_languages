<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\UpdateReservationResource;
use App\Models\Flat;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function create(CreateReservationRequest $request){
        $validated = $request->validated();
        $flat=Flat::with('reservations')->find($request['flat_id']);
        if($flat &&$flat->status=='available') {
            $conflict = $flat->reservations()->
            where(function ($query) use ($validated) {
                $query->wherebetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orwherebetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orwhere(function ($query1) use ($validated) {
                        $query1->where('start_time', '<=', $validated['end_time'])
                            ->where('end_time', '>=', $validated['start_time']);
                    })
                    ->orwhere(function ($query1) use ($validated) {
                        $query1->where('start_time', '>=', $validated['start_time'])
                            ->where('end_time', '<=', $validated['end_time']);
                    });

            })->exists();
        }
        else{
            return response()->json([
                'message'=>'flat is not available because its'.$flat->status,
            ]);
        }

        if ($conflict) {
            return response()->json([
                'message' => 'This flat is already reserved during the selected time.'
            ], 400);
        }

        $start = Carbon::parse($validated['start_time']);
        $end   = Carbon::parse($validated['end_time']);

        $diffInDays = $start->diffInDays($end);

        if($diffInDays >= 30) {
            $validated['price'] = $validated['price'] * ($diffInDays - 2);
        }
        if($diffInDays >= 180){
            $validated['price']=$validated['price'] * ($diffInDays-10);
        }
        if($diffInDays >= 365){

            $validated['price']=$validated['price'] * ($diffInDays-30);
            $flat->status='booked';
            $flat->save();
        }
        $validated['price']=$validated['price']*$diffInDays;

        $validated['user_id']=auth()->id();
        $validated['status'] = $validated['status'] ?? 'pending';//ليش عملت هيك لان القيمة رح ترجع لتبع الفلاتر null بس انا بدي ياها ترجع pending لان انا بال validation ماني جابرو يدخل قيمة فلهيك رح يرجع null
$reservation=Reservation::create($validated);
return response()->json([new ReservationResource($reservation)]);
    }

    /////////////////////////////andrew was here ////////////////////////////
    public function update( UpdateReservationRequest $request){
        $validated = $request->validated();
        $reservation=Reservation::find($validated['id']);
        if(!$reservation){
            return response()->json([
                'message'=>'Reservation not found'
            ],404);
        }

        $flat=Flat::with('reservations')->find($reservation['flat_id']);



            $conflict = $flat->reservations()
                ->where('id','!=',$reservation->id)->
            where(function ($query) use ($validated) {
                $query->wherebetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orwherebetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orwhere(function ($query1) use ($validated) {
                        $query1->where('start_time', '<=', $validated['end_time'])
                            ->where('end_time', '>=', $validated['start_time']);
                    })
                    ->orwhere(function ($query1) use ($validated) {
                        $query1->where('start_time', '>=', $validated['start_time'])
                            ->where('end_time', '<=', $validated['end_time']);
                    });

            })->exists();



        if ($conflict) {
            return response()->json([
                'message' => 'This flat is already reserved during the selected time.'
            ], 400);
        }

//        if(!$reservation){
//            return response()->json([
//                'message'=>'reservation not found'
//            ],404);
//        }
//        $flat=$reservation->flat;
//    if ($validated['start_time'] <= $flat->available_date) {
//        return response()->json([
//            'message'=>'Start time must be after or equal to flat available_date.'
//        ],422);
//    }
//    if($validated['end_time'] <= $flat->available_date){
//        return response()->json([
//            'message'=>'End time must be after or equal to flat available_date.'
//        ],422);//422 الطلب صحيح بس البيانات مابيمشي حالها
//    }
        $start = Carbon::parse($validated['start_time']);
        $end   = Carbon::parse($validated['end_time']);
        $diffInDays = $start->diffInDays($end);
        $validated['price']=  $flat['price']*$diffInDays;
        if($diffInDays >= 30) {
            $validated['price'] = $flat['price'] * ($diffInDays - 2);
        }
        if($diffInDays >= 180){
            $validated['price']=$flat['price'] * ($diffInDays-10);
        }
        if($diffInDays >= 365){

            $validated['price']=$flat['price'] * ($diffInDays-30);
            $flat->status='booked';
            $flat->save();
        }

        $reservation->update($validated);
        $reservation->status='pending';
        $reservation->save();
        return response()->json([new  UpdateReservationResource($reservation)]);
    }
    public function CancelReservation($id){
        $reservation=Reservation::find($id);
        $userId=auth()->id();

        if(!$reservation){

            return response()->json([
                'message'=>'Reservation not found'
            ]);

        }
        if($reservation->user_id!==$userId){
            return response()->json([
                'message'=>'You do not have the authority to cancel this booking.'
            ]);
        }
        $reservation->status='cancelled';
        $reservation->save();
        return response()->json([
            'message'=>'Reservation cancelled successfully'
        ]);

    }
    /////////////////////////andrew was here ////////////////////////////////////

    public function showReservation(){
        $userId=auth()->id();
$user=User::with('reservations')->find($userId);
return response()->json([ReservationResource::collection($user->reservations)]);
    }
}
