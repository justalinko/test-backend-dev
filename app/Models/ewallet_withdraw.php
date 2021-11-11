<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ewallet_withdraw extends Model
{
    use HasFactory;

    protected $table = 'ewallet_withdraws';
    protected $primaryKey = 'id';

    protected $fillable = [
        'amount',
        'destination',
        'status',
        'user_id',
        'invoice',
        'status',
        'description'
    ];
}
