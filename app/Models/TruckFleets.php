<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckFleets extends Model
{
    use HasFactory;
  
    protected $fillable=[
        'id',
        'registration',
        'model_id',
        'type_id',
        'year',
        'inspection_date',
        'max_cargo',
    ];
}


