<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garage extends Model
{
    use HasFactory;
    protected $table = 'garage';
    protected $fillable=[
        'id',
        'date_report',
        'date_start',
        'date_end',
        'status',
        'registration_id',
        'breakdowns_id',
        'garage_status_id',
        'reason',
        'created_at',
        'updated_at', 
    
    ];
}
