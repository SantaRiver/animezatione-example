<?php

namespace App\Http\Controllers;

use App\Models\EOS\Kleos;
use App\Models\NFTsModel;
use App\Models\WaxUserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BurnCardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (!$request->get('transaction_id')) {
            return response()->json(['status' => 'error', 'message' => 'transaction_id is empty']);
        }
        $user = session('user', false);
        $user = WaxUserModel::query()->where('userAccount', $user['userAccount'])->first();
        $template_id = $request->get('template_id');
        $asset_id = $request->get('asset_id');

        $url = 'https://wax.greymass.com/v1/history/get_transaction?id='.$request->get('transaction_id');
        $response = Http::get($url)->json();
        foreach ($response['actions'] as $action) {
            if ($action['action_ordinal'] == 1) {
                $act = $action['act'];
                if ($act['name'] != 'burnasset' ||
                    $act['data']['asset_owner'] != $user['userAccount'] ||
                    $act['data']['asset_id'] != $asset_id) {
                    return response()->json(
                        ['status' => 'error', 'message' => 'Failed to verify the authenticity of the transaction']
                    );
                }
            }
        }

        $card = NFTsModel::query()->firstWhere('template_id', $template_id);

        $kleos = new Kleos();
        $response = $kleos->transfer($user['userAccount'], $card['burning'], 'ANI', 'anionereward', 'Reward');
        if ($response['response']->transaction_id) {
            return response()->json(
                ['status' => 'success', 'transaction_id' => $response['response']->transaction_id]
            );
        }

        return response()->json(['status' => 'error', 'message' => 'Internal server error']);
    }
}
