<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BurnLog extends Model
{
    use HasFactory;

    protected $table = 'burn_log';

    protected $fillable = [
        'user_id',
        'nft_id',
        'count',
    ];
}
