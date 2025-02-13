<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutualNavHistory extends Model
{
    protected $table = 'mutual_nav_history';  // The table that stores NAV data
    protected $fillable = ['fundname_id', 'nav', 'date'];  // Adjust based on your table structure
}
