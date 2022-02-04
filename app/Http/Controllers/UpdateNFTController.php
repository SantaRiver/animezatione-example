<?php

namespace App\Http\Controllers;

use App\Http\Requests\NFTUpdateRequest;
use App\Models\NFTMarket;
use App\Models\NFTsModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class UpdateNFTController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  NFTUpdateRequest  $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $nft_data = $request->all();

        $nft_data['active'] = (isset($nft_data['active'])) ? 1 : 0;
        NFTMarket::query()->firstWhere(['nft_id' => $nft_data['id']])->update(
            [
                'price_usd' => $nft_data['price_usd'],
                'price_ani' => $nft_data['price_usd'],
                'on_sale' => (isset($nft_data['on_sale']) && $nft_data['price_usd'] > 0) ? 1 : 0,
            ]
        );
        unset($nft_data['price_usd']);
        unset($nft_data['on_sale']);
        $nft = NFTsModel::query()->find($nft_data['id']);
        $nft->update($nft_data);
        Artisan::call('price:recalculation');
        return response()->json(['status' => 'success', 'card' => $nft]);
    }
}
