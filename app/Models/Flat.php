<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flat extends Model {

public function user()
{
    return $this->belongsTo(User::class);
}
public function reservations(){
    return $this->hasMany(Reservation::class);
}
public function ratings(){
    return $this->hasMany(FlatRating::class);
}
public function favorites()
{
return $this->hasMany(Favorite::class);
}
    protected $fillable = [
        'governorate', 'city', 'price', 'rooms', 'space',
        'floor', 'has_elevator', 'is_furnished', 'user_id', 'status', 'address', 'available_date','flat_image','section'
    ];

}
