<?php

namespace App\Http\Controllers;

use App\Models\EOS\Kleos;
use App\Models\Log\RewardTransactions;
use App\Models\NFTsModel;
use App\Models\PackRewards;
use App\Models\WaxUserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UnpackingController extends Controller
{

    public function index()
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);
        $user->updateInventory();
        return \view(
            'redesign.pages.unpacking',
        );
    }

    public function ofList(Request $request)
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);
        $collection = $user->inventory()
            ->join('nft', 'users_inventory.card_id', '=', 'nft.id')
            ->whereIn('assets', ['pack'])
            ->where('name', 'like', '%'.$request->get('query', ''))
            ->orderBy('assets')
            ->paginate('10');
        foreach ($collection->items() as &$item) {
            $packRewards = PackRewards::query()->where('pack_id', $item['id'])
                ->join('nft', 'pack_rewards.card_id', '=', 'nft.id')
                ->get();
            $totalCount = $packRewards->sum('count');
            $packRewardsChance = [];
            foreach ($packRewards->toArray() as $reward) {
                $packRewardsChance[$reward['card_id']] = $reward['count'] / $totalCount * 100;
            }
            $item['pack_reward'] = $packRewards->toArray();
            $item['pack_reward_chance'] = $packRewardsChance;
        }
        return view(
            'redesign.pages.cards',
            [
                'collection' => $collection
            ]
        );
    }

    public function unpack(Request $request): JsonResponse
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);

        if (!$request->get('transaction_id')) {
            return response()->json(['status' => 'error', 'message' => 'transaction_id is empty']);
        }

        $sendTransactionId = $request->get('transaction_id', '');

        (new RewardTransactions(
            [
                'user_id' => $user->id,
                'transaction_id' => $sendTransactionId,
                'action' => 'send',
            ]
        )
        )->save();

        $checkTransactionUrl = "https://wax.greymass.com/v1/history/get_transaction?id=$sendTransactionId";
        $transaction = Http::get($checkTransactionUrl)->json();
        foreach ($transaction['actions'] as $action) {
            if ($action['act']['name'] == 'transfer') {
                $transfer = $action['act']['data'];
                if ($transfer['to'] != 'anionereward') {
                    return response()->json(
                        ['status' => 'error', 'message' => 'The pack did not reach the desired wallet']
                    );
                }
            }
        }

        /*
         * Открываем пак
         * */

        $card = NFTsModel::query()
            ->where('assets', 'pack')
            ->firstWhere('template_id', $request->get('template_id'));

        $packRewards = PackRewards::query()
            ->where('pack_id', $card->id)
            ->join('nft', 'card_id', '=', 'nft.id')
            ->get(['card_id', 'count', 'type', 'template_id', 'name']);
        $cardCount = $packRewards->sum('count');
        foreach ($packRewards as $reward) {
            $reward->chance = $reward->count / $cardCount;
        }

        $rewardResource = [
            'reward' => $packRewards,
            'count_card' => $cardCount
        ];

        $roulette = [];
        $prizeList = [];
        foreach ($rewardResource['reward'] as $reward) {
            for ($i = 0; $i < $reward->count; $i++) {
                if ($reward->type == 'random') {
                    $roulette[] = $reward->template_id;
                }
            }
            if ($reward->type == 'always') {
                $prizeList[] = $reward->template_id;
            }
        }

        for ($j = 0; $j < 4; $j++) {
            $prizeIndex = rand(0, sizeof($roulette) - 1);
            $prizeList[] = $roulette[$prizeIndex];
            unset($roulette[$prizeIndex]);
        }

        $prizeTemplateId = implode(',', $prizeList);
        $param = [
            'collection_name' => 'animezatione',
            'owner' => 'anionereward',
            'template_whitelist' => $prizeTemplateId,
            'page' => 1,
            'limit' => 1000,
            'order' => 'desc',
            'sort' => 'asset_id',
        ];
        $prizeAssetUrl = "https://wax.api.atomicassets.io/atomicassets/v1/assets?".http_build_query($param);
        $prizeAssetResource['success'] = false;
        while (!$prizeAssetResource['success']) {
            $prizeAssetResource = Http::get($prizeAssetUrl)->json();
        }

        $prizeAssetIds = [];
        $prizeTemplateAsset = [];
        foreach ($prizeList as $prizeTemplateId) {
            foreach ($prizeAssetResource['data'] as $prizeAsset) {
                if ($prizeAsset['template']['template_id'] == $prizeTemplateId &&
                    !in_array($prizeAsset['asset_id'], $prizeAssetIds)) {
                    $prizeAssetIds[] = $prizeAsset['asset_id'];
                    $prizeTemplateAsset[] = [
                        'asset_id' => $prizeAsset['asset_id'],
                        'template_id' => $prizeAsset['template']['template_id']
                    ];
                    break;
                }
            }
        }
        $kleos = new Kleos();
        $transferResponse = $kleos->transferAssets($user->userAccount, $prizeAssetIds)['response'];
        if(!$transferResponse->transaction_id){
            (new RewardTransactions(
                [
                    'user_id' => $user->id,
                    'action' => 'error',
                    'reward' => $prizeTemplateAsset,
                ]
            ))->save();
            return response()->json(['status' => 'error', 'message' => 'Transaction failed']);
        }

        $RewardTransactions = new RewardTransactions(
            [
                'user_id' => $user->id,
                'action' => 'get',
                'transaction_id' => $transferResponse->transaction_id,
                'reward' => json_encode($prizeTemplateAsset),
            ]
        );
        $RewardTransactions->save();

        foreach ($prizeTemplateAsset as $rewardAsset) {
            $card = NFTsModel::query()->firstWhere('template_id', $rewardAsset['template_id']);
            $packReward = PackRewards::query()
                ->where('pack_id', $request->get('pack_id'))
                ->where('card_id', $card->id)
                ->first();
            if ($packReward->type == 'random') {
                if ($packReward->count > 1) {
                    $packReward->count--;
                    $packReward->save();
                } else {
                    $packReward->delete();
                }
            }
        }
        $cardReward = [];
        foreach ($prizeTemplateAsset as $rewardAsset) {
            $cardReward[] = NFTsModel::query()
                ->firstWhere('template_id', $rewardAsset['template_id'])
                ->toArray();
        }

        return response()->json(
            [
                'status' => 'success',
                'transaction_id' => $request->get('transaction_id'),
                'reward' => $cardReward,
            ]
        );
    }
}
