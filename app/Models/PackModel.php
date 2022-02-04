<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackModel extends Model
{
    use HasFactory;

    protected $table = 'packs';

    protected $fillable = [
        'user_id',
        'template_id',
        'name',
        'count',
    ];
}
