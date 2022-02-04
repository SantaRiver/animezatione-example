<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveStacking extends Model
{
    use HasFactory;

    protected $table = 'active_stacking';

    protected $fillable = [
        'user_id',
        'status',
        'value',
        'reward',
        'reward',
        'end_time',
    ];
}
