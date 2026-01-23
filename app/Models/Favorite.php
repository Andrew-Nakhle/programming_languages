<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable=['user_id','flat_id'];
    public function flat(){
        return $this->belongsTo(Flat::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}

