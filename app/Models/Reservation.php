<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    public function flat(){
        return $this->belongsTo(Flat::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    protected $fillable=['start_time','end_time','user_id','flat_id','price','status'];
}
