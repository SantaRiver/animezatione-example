<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketTransaction extends Model
{
    use HasFactory;

    protected $table = 'market_transactions';

    protected $fillable = [
        'user_id',
        'transaction_id',
        'template_id',
        'status',
        'message',
    ];
}
