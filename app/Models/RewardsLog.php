<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardsLog extends Model
{
    use HasFactory;

    protected $table = 'rewards_log';

    protected $fillable = [
        'pack_template_id',
        'asset_id',
        'user_id',
        'reward',
    ];
}
