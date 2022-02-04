<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cards;
use App\Models\NFTsModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetCardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $card = NFTsModel::query()
            ->where('template_id', $request->get('template_id'))
            ->join('nft_markets', 'nft.id', '=', 'nft_markets.nft_id')
            ->first();
        return response()->json(['status' => 'success', 'card' => $card]);
    }
}
