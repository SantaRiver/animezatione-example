<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInventory extends Model
{
    use HasFactory;

    protected $table = 'users_inventory';

    protected $fillable = [
        'user_id',
        'card_id',
        'asset_id',
        'mint',
    ];
}
