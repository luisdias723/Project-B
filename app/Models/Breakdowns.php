<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breakdowns extends Model
{
    use HasFactory;
    
    protected $fillable=[
        'id',
        'type',
        'created_at',
        'updated_at'
    ];
}
