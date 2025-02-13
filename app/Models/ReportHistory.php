<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{
    protected $table = 'report_history';
    protected $fillable = ['fundname_id', 'date', 'unit', 'price', 'status'];

    // Define the relationship to MutualFund_Master
    public function fund()
    {
        return $this->belongsTo(\App\Models\MutualFund_Master::class, 'fundname_id');
    }
}
