<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardsResourceCollection;
use App\Models\Cards;
use App\Models\EOS\Kleos;
use App\Models\Log\MarketTransaction;
use App\Models\NFTsModel;
use App\Models\WaxUserModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MarketController extends Controller
{
    public function index(): Factory|View|Application
    {
        return view(
            'redesign.pages.market',
        );
    }

    public function ofList(Request $request): Factory|View|Application
    {
        $availabilityCards = [];
        $param = [
            'collection_name' => 'animezatione',
            'owner' => 'anionereward',
            'page' => '1',
            'limit' => 1500,
            'order' => 'desc',
            'sort' => 'asset_id',
        ];
        $assetsUrl = 'https://wax.api.atomicassets.io/atomicassets/v1/assets?'.http_build_query($param);
        $assetsResponse['success'] = false;
        while (!$assetsResponse['success']) {
            $assetsResponse = Http::get($assetsUrl)->json();
        }
        foreach ($assetsResponse['data'] as $asset) {
            $availabilityCards[$asset['template']['template_id']][] = $asset['asset_id'];
        }

        $availabilityTemplateId = array_keys($availabilityCards);

        $cards = NFTsModel::query()
            ->where('active', '=', 1)
            ->join('nft_markets', 'nft.id', '=', 'nft_markets.nft_id')
            ->whereIn('template_id', $availabilityTemplateId)
            ->where('on_sale', '=', 1)
            ->where('name', 'like', '%'.$request->get('query', ''))
            ->orderBy('price_ani')
            ->paginate('10');

        return \view(
            'redesign.pages.cards',
            [
                'collection' => $cards,
                'availabilityCards' => $availabilityCards,
            ]
        );
    }

    public function buy_card(Request $request): JsonResponse
    {

        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);

        $request->validate(
            [
                'transaction_id' => 'string|required',
                'template_id' => 'string|required',
            ]
        );
        (new MarketTransaction(
            [
                'user_id' => $user['id'],
                'transaction_id' => $request->get('transaction_id'),
                'template_id' => $request->get('template_id'),
                'status' => 'paid',
            ]
        ))->save();

        $card = NFTsModel::query()
            ->where('template_id', $request->get('template_id'))
            ->join('nft_markets', 'nft.id', '=', 'nft_markets.nft_id')
            ->first();

        $url = 'https://wax.greymass.com/v1/history/get_transaction?id='.$request->get('transaction_id');
        $response['actions'] = [];
        while (!sizeof($response['actions']) <= 1) {
            $response = Http::get($url)->json();
        }
        $act = $response['actions'][0]['act'];
        if ($act['account'] != 'anionereward' ||
            $act['data']['from'] != $user['userAccount'] ||
            $act['data']['to'] != 'anionereward' ||
            $act['data']['amount'] != $card['price_ani']) {
            return response()->json(
                ['status' => 'error', 'message' => 'Unable to establish the authenticity of the transaction']
            );
        }

        $assetParam = [
            'collection_name' => 'animezatione',
            'template_id' => $card['template_id'],
            'owner' => 'anionereward',
            'page' => 1,
            'limit' => 1,
            'order' => 'desc',
            'sort' => 'asset_id',
        ];
        $assetUrl = 'https://wax.api.atomicassets.io/atomicassets/v1/assets?'.http_build_query($assetParam);
        $assetResponse['success'] = false;
        while (!$assetResponse['success']) {
            $assetResponse = Http::get($assetUrl)->json();
        }
        $asset = $assetResponse['data'][0];

        $kleos = new Kleos();
        $transferResponse = $kleos->transferAssets($user->userAccount, [$asset['asset_id']])['response'];
        if ($transferResponse->transaction_id) {
            (new MarketTransaction(
                [
                    'user_id' => $user['id'],
                    'transaction_id' => $transferResponse->transaction_id,
                    'template_id' => $request->get('template_id'),
                    'status' => 'executed',
                ]
            ))->save();
        } else {
            (new MarketTransaction(
                [
                    'user_id' => $user['id'],
                    'template_id' => $request->get('template_id'),
                    'status' => 'error',
                    'message' => 'Impossible to send asset'
                ]
            ))->save();
        }

        return response()->json($transferResponse);
    }
}
