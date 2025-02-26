<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Auth extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'first_name', 
        'last_name', 
        'email', 
        'phone_number', 
        'birthdate', 
        'password',
    ];

    protected $dates = ['birthdate'];

    public $timestamps = false; // Disable timestamps

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
