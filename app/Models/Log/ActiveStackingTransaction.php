<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveStackingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'active_stacking_id',
        'status',
        'transaction_id',
    ];
}
