<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debug extends Model
{
    use HasFactory;
    protected $table = 'debug_schedule';

    protected $fillable = [
        'debug',
    ];
}
