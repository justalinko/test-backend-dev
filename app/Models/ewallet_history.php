<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ewallet_history extends Model
{
    use HasFactory;

    protected $table = 'ewallet_histories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'amount',
        'user_id',
        'type',
        'description'
    ];


    public function saveHistory($amount, $type)
    {
        $desc = 'Transaction created ' . $amount . ' Type : ' . $type . ' at ' . date('Y-m-d H:i');
        $user_id = Auth::user()->id;

        return static::create(['amount' => $amount, 'user_id' => $user_id, 'type' => $type, 'description' => $desc]);
    }
    public function saveHistoryById($id, $amount, $type)
    {
        $desc = 'Transaction created ' . $amount . ' Type : ' . $type . ' at ' . date('Y-m-d H:i');
        $user_id = $id;

        return static::create(['amount' => $amount, 'user_id' => $user_id, 'type' => $type, 'description' => $desc]);
    }
}
