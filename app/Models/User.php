<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'user';
    protected $table = 'users';
    protected $fillable = [
        'first_name', 
        'last_name', 
        'email', 
        'phone', 
        'birthdate', 
        'password',
        'pan_no',
        'avatar',
    ];
   
}
