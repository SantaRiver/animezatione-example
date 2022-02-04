<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NFTsModel;
use App\Models\PackRewards;
use Illuminate\Http\JsonResponse;

class GetPackRewardChanceController extends Controller
{
    public function __invoke($packTemplateId): JsonResponse
    {
        $card = NFTsModel::query()
            ->where('assets', 'pack')
            ->firstWhere('template_id', $packTemplateId);

        $packRewards = PackRewards::query()
            ->where('pack_id', $card->id)
            ->join('nft', 'card_id', '=', 'nft.id')
            ->get(['card_id', 'count', 'type', 'template_id', 'name']);
        $cardCount = $packRewards->sum('count');
        foreach ($packRewards as $reward){
            $reward->chance = $reward->count / $cardCount;
        }

        return response()->json(
            [
                'status' => 'success',
                'reward' => $packRewards,
                'count_card' => $cardCount
            ]
        );
    }
}
