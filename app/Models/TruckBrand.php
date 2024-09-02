<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckBrand extends Model
{
    use HasFactory;

    protected $fillable=[
        'id',
        'brand',    
    ];
}
