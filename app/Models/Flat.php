<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flat extends Model
{
    protected $fillable = [
        'governorate', 'city', 'price', 'rooms', 'space',
        'floor', 'has_elevator', 'is_furnished', 'user_id'
    ];

}
