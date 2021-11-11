<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ewallet_topup extends Model
{
    use HasFactory;

    protected $table = 'ewallet_topups';
    protected $primaryKey = 'id';

    protected $fillable = [
        'amount',
        'user_id',
        'payment_method',
        'billing_id',
        'invoice',
        'status',
        'description'
    ];


}
