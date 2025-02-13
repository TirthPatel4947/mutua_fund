<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutualFund_Master extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'mutualfund_master';

    // Fillable columns - allow mass assignment for these fields
    protected $fillable = [
        'fundcode',
        'fundname',
        'status',
        'last_status_updated'
    ];

    // Define the relationship between MutualFund_Master and Nav models
    public function nav()
    {
        return $this->hasMany(Nav::class, 'fundname_id', 'id');
    }
    // Define the relationship to MutualFundMaster
    public function fund()
    {
        return $this->belongsTo(\App\Models\MutualFund_Master::class, 'fundname_id');
    }
}
