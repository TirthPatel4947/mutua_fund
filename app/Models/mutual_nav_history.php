<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mutual_nav_history extends Model
{
    protected $table = 'mutual_nav_history';  // Replace with your actual table name
    protected $fillable = ['fundname_id', 'date', 'nav']; // Specify relevant columns
}
