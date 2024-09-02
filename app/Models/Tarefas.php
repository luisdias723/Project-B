<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarefas extends Model
{
    use HasFactory;
    
    protected $fillable=[
        'id',
        'user_id',
        'name',
        'checklist_id',
        'data_inicio',
        'data_limite',
    ];
}
