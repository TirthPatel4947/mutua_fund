<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buy extends Model
{
    protected $table = 'mutualfund_master'; // Ensure this table name matches the one in your database
    protected $fillable = ['id', 'fundname']; // Specify relevant columns
}
