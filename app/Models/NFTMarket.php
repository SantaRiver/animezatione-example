<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFTMarket extends Model
{
    use HasFactory;

    protected $table = 'nft_markets';

    protected $fillable = [
        'nft_id',
        'on_sale',
        'price_usd',
        'price_ani',
    ];
}
