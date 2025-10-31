<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'classes';
    
    protected $fillable = [
        'name',
        'code',
        'description',
        'capacity',
        'level',
        'program',
    ];
}
