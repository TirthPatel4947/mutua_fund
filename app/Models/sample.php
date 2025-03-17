<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    use HasFactory;

    protected $table = 'sample'; // Ensure the table name is correct

    protected $fillable = [
        'user_id',
        'portfolio_id',
        'fundname_id',
        'date',
        'price',
        'total',
        'status'
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relationship with Portfolio model
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id', 'id');
    }

    // Relationship with MutualFund_Master model
    public function fund()
    {
        return $this->belongsTo(MutualFund_Master::class, 'fundname_id', 'id');
    }
}
