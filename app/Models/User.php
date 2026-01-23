<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }
public function inComingReservations(){
        return $this->hasManyThrough(
            Reservation::class,
            Flat::class,
            'user_id',
            'flat_id',
            'id',
            'id'
        );
}
    public function flats()
    {
return $this->hasMany(Flat::class);
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function ratings()
    {
        return $this->hasMany(FlatRating::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function generate_otp_code(){
        $this->timestamps = false;
        $this->otp_code=rand(10000,99999);
        $this->otp_expired_at=now()->addMinutes(65);
        $this->save();

    }
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'password',
        'birth_date',
        'id_card_path',
        'avatar_path',
        'country',
        'gender',
        'is_admin',
        'status',
        'otp_expired_at',
        'otp_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();

    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'approval' => $this->approval_status,
        ];

    }
}
