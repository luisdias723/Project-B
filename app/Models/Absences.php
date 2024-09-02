<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absences extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'start_date',
        'end_date',
        'type',
        'created_by',
        'confirmed',
        'created_at',
        'updated_at',
    ];
}
