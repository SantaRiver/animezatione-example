<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackRewards extends Model
{
    use HasFactory;

    static function getChance($packId): Collection|array
    {
        $pack = PackModel::query()->find($packId);
        $packCard = NFTsModel::query()->firstWhere('template_id', $pack->template_id);
        return PackRewards::query()->where('pack_id', $packCard->id)->get();
    }
}
