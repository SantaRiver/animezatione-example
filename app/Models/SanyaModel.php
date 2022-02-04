<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanyaModel extends Model
{
    use HasFactory;

    protected $table='sanya';

    protected $fillable = [
        'alko',
        'taback'
    ];
}
