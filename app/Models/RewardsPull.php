<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardsPull extends Model
{
    use HasFactory;

    protected $table = 'packs_pull';

    protected $fillable = [
        'nft_id',
        'count',
        'pack_template_id',
    ];
}
