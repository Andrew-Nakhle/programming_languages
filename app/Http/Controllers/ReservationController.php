<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\CreateReservationRequest;
use App\Http\Requests\Reservation\UpdateReservationRequest;
use App\Http\Resources\Reservation\ReservationResource;
use App\Http\Resources\Reservation\UpdateReservationResource;
use App\Models\Flat;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\ReservationUpdate;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function create(CreateReservationRequest $request)
    {
        $user=auth()->user();
        $validated = $request->validated();
        $flat = Flat::with('reservations')->find($request['flat_id']);
//        checkReservationConflict( $flat, null, $validated['start_time'],$validated['end_time']);
        if(!$flat){
            return response()->json([
                'message' => 'Flat was not found'
            ],404);
        }
        if($flat->status!='available'){
            return response()->json([
                'message' => 'flat is not available because its' . $flat->status,

            ],400);
        }
        if ($flat->user_id==$user->id){
            return response()->json([
                'message' => 'You cannt reserve your own flat'
            ]);
        }

            $conflict = $flat->reservations()->
            where('status','approved')->
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

        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);

        $diffInDays = $start->diffInDays($end);

        if ($diffInDays >= 30) {
            $validated['price'] = $validated['price'] * ($diffInDays - 2);
        }
        if ($diffInDays >= 180) {
            $validated['price'] = $validated['price'] * ($diffInDays - 10);
        }
        if ($diffInDays >= 365) {

            $validated['price'] = $validated['price'] * ($diffInDays - 30);
            $flat->status = 'booked';
            $flat->save();
        }
        $validated['price'] = $validated['price'] * $diffInDays;

        $validated['user_id'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'pending';//ليش عملت هيك لان القيمة رح ترجع لتبع الفلاتر null بس انا بدي ياها ترجع pending لان انا بال validation ماني جابرو يدخل قيمة فلهيك رح يرجع null
        $reservation = Reservation::create($validated);

        return response()->json(['message' => 'please wait until owner accept your reservation',
            'reservation' => new ReservationResource($reservation)]);
    }

    /////////////////////////////andrew was here ////////////////////////////
    public function update(UpdateReservationRequest $request)
    {
        $user=auth()->user();
        $validated = $request->validated();
        $reservation = Reservation::find($validated['id']);
        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);
        }

        $flat = Flat::with('reservations')->find($reservation['flat_id']);
        if($reservation->status=='rejected'  ||  $reservation->status=='canceled'){
            return response()->json([
                'message' => 'Reservation is already canceled or rejected'
            ]);
        }
        if($flat->status!='available'){
            return response()->json([
                'message' => 'flat is not available because its' . $flat->status,

            ],400);
        }
        if ($flat->user_id==$user->id){
            return response()->json([
                'message' => 'You cannt reserve your own flat'
            ]);
        }

        $conflict = $flat->reservations()
            ->where('id', '!=', $reservation->id)->
                where('status',  'approved')->
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

//        ],422);//422 الطلب صحيح بس البيانات مابيمشي حالها
//    }
        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);
        $diffInDays = $start->diffInDays($end);
        $validated['price'] = $flat['price'] * $diffInDays;
        if ($diffInDays >= 30) {
            $validated['price'] = $flat['price'] * ($diffInDays - 2);
        }
        if ($diffInDays >= 180) {
            $validated['price'] = $flat['price'] * ($diffInDays - 10);
        }
        if ($diffInDays >= 365) {

            $validated['price'] = $flat['price'] * ($diffInDays - 30);
            $flat->status = 'booked';
            $flat->save();
        }

        $reservation->update($validated);
        $reservation->user->notify(new ReservationUpdate($reservation));
        $reservation->status = 'pending';
        $reservation->save();
        return response()->json(['message'=>'please wait until owner accept your update',
            'reservation' => new ReservationResource($reservation)], 200);
    }
        public function allReservationsForUserFlats()
    {
        ////طريقة اولى باستخدام whereIn تابع pluck بحبلي عمود واحد بس من جدول
//        $user = auth()->user();
//
//        $reservations = Reservation::whereIn('flat_id', $user->flats->pluck('id'))
//            ->where('status', 'pending')
//            ->get();
///////طريقة تانية باسخدام العلاقات
//        $reservations = Reservation::whereHas('flat', function ($query) {
//            $query->where('user_id', auth()->id());
//        })
//            ->where('status', 'pending')
//            ->get();
        $reservation=auth()->user()->inComingReservations()->where('reservations.status','pending')->get();
            if ($reservation->isEmpty()) {
                return response()->json([
                    'message' => 'No reservations found'
                ]);
            }

            return response()->json($reservation);
    }

    public function CancelReservation($id)
    {
        $reservation = Reservation::with('flat')->find($id);
        $userId = auth()->id();

        if (!$reservation) {

            return response()->json([
                'message' => 'Reservation not found'
            ]);

        }
        if ($reservation->flat->user_id !== $userId) {
            return response()->json([
                'message' => 'You do not have the authority to cancel this booking.'
            ]);
        }
        if ($reservation->status == 'cancelled') {
            return response()->json([
                'message' => 'Reservation already cancelled.'
            ]);
        }
        $reservation->status = 'cancelled';
        $reservation->save();
        return response()->json([
            'message' => 'Reservation cancelled successfully',
            'reservation' => new ReservationResource($reservation)
        ]);

    }

    /////////////////////////andrew was here ////////////////////////////////////

    public function showReservation()
    {
        $userId = auth()->id();
        $user = User::with('reservations')->find($userId);

        return response()->json(ReservationResource::collection($user->reservations));
    }

//////////////////andrew was here /////////////////////
    public function approveReservation($id)
    {
        $userId = auth()->id();
        $reservation = Reservation::with('flat')->find($id);
        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);
        }
        if ($userId != $reservation->flat->user_id) {
            return response()->json([
                'message' => 'You do not have the authority to approve this reservation.'
            ], 403);
        }
        if($reservation->status == 'approved'){
            return response()->json([
                'message' => 'Reservation already approved'
            ]);
        }

        $reservation->status = 'approved';
        $reservation->save();

        $conflicts=$reservation->where('flat_id',$reservation->flat_id)
            ->where('id','!=',$id)
            ->where(function ($query) use ($reservation) {
                $query->where('start_time','<',$reservation->end_time)
                    ->where('end_time','>=',$reservation->start_time);
            })->get();

        foreach ($conflicts as $coflict){
$coflict->status='rejected';
$coflict->save();
        }

        $reservation->save();
        return response()->json([
            'message' => 'Reservation approved successfully',
            'reservation' => new ReservationResource($reservation)
        ], 200);
    }

    public function rejectReservation($id)
    {
        $userId = auth()->id();
        $reservation = Reservation::with('flat')->find($id);
        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);

        }
        if ($userId != $reservation->flat->user_id) {
            return response()->json([
                'message' => 'You do not have the authority to reject this reservation.'
            ], 403);
        }
        if($reservation->status == 'rejected'){
            return response()->json([
                'message' => 'Reservation already rejected'
            ],409);}

        $reservation->status = 'rejected';
        $reservation->save();
        return response()->json([
            'message' => 'Reservation rejected successfully'
        ], 200);
    }

}
