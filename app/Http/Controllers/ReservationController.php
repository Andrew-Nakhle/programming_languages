<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Flat;
use App\Models\Reservation;
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
        if($diffInDays >= 365){
            $flat->status='booked';
            $flat->save();
        }
        $validated['user_id']=auth()->id();
        $validated['status'] = $validated['status'] ?? 'pending';//ليش عملت هيك لان القيمة رح ترجع لتبع الفلاتر null بس انا بدي ياها ترجع pending لان انا بال validation ماني جابرو يدخل قيمة فلهيك رح يرجع null
$reservation=Reservation::create($validated);
return response()->json([new ReservationResource($reservation)]);
    }
}
