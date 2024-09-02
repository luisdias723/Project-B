<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garage_Status extends Model
{
    use HasFactory;

    protected $fillable=[
        'id',
        'description',
        'created_at',
        'updated_at',  
    ];
}
