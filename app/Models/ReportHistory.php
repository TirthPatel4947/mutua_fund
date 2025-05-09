<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{
    protected $table = 'report_history';
    protected $fillable = ['user_id','portfolio_id','fundname_id', 'date', 'unit', 'price', 'total','status'];

    // Define the relationship to MutualFund_Master
    public function fund()
    {
        return $this->belongsTo(MutualFund_Master::class, 'fundname_id');
    }
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id');
    }
}
