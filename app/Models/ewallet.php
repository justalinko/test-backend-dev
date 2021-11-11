<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ewallet extends Model
{
    use HasFactory;
    
    protected $table = 'ewallets';
    protected $primaryKey = 'id';
    
}
