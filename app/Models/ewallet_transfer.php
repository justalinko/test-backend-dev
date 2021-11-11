<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ewallet_transfer extends Model
{
    use HasFactory;

    protected $table = 'ewallet_transfers';
    protected $primaryKey = 'id';

    protected $guarded = [];
}
