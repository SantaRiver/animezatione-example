<?php

namespace App\Models\Faucet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaucetTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'faucet_id'
    ];
}
