<?php
namespace App\Http\Controllers;
use App\Models\Flat;
use Carbon\Carbon;

trait ReservationTrait
{


    public function checkReservationConflict(Flat $flat, $reservationId, $start_time, $end_time)
    {
        return $flat->reservations()
            ->where('id','!=',$reservationId)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time])
                    ->orWhere(function ($q) use ($start_time, $end_time) {
                        $q->where('start_time', '<=', $end_time)
                            ->where('end_time', '>=', $start_time);
                    })
                    ->orWhere(function ($q) use ($start_time, $end_time) {
                        $q->where('start_time', '>=', $start_time)
                            ->where('end_time', '<=', $end_time);
                    });
            })
            ->exists();
    }



    public function calculatePrice($start_time, $end_time, Flat $flat)
    {


        $start = Carbon::parse($start_time);
        $end = Carbon::parse($end_time);
        $diffInDays = $start->diffInDays($end);


        $price = $flat->price * $diffInDays;

        if ($diffInDays >= 30) {
            $price *= ($diffInDays - 2);
        }
        if ($diffInDays >= 180) {
            $price *= ($diffInDays - 10);
        }
        if ($diffInDays >= 365) {
            $price *= ($diffInDays - 30);
            $flat->status = 'booked';
            $flat->save();
        }

        return $price;
    }

}




