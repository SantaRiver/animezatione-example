<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class NFTsModel extends Model
{
    use HasFactory;

    protected $table = 'nft';

    protected $fillable = [
        'name',
        'template_id',
        'path',
        'preview',
        'assets',
        'rarity',
        'benefit',
        'burning',
        'active',
        'description',
    ];

}
