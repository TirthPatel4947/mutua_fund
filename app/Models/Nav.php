<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nav extends Model
{
    protected $table = 'mutual_nav_history';
    protected $fillable = ['fundname_id', 'nav', 'date', 'batch_no'];
    public $timestamps = false;

    // Define the inverse relationship to the MutualFund_Master model
    public function mutualFundMaster()
    {
        return $this->belongsTo(MutualFund_Master::class, 'fundname_id', 'id');
    }
}
