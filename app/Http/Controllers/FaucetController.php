<?php

namespace App\Http\Controllers;

use App\Models\EOS\Kleos;
use App\Models\Faucet\Faucet;
use App\Models\Faucet\FaucetTransaction;
use App\Models\WaxUserModel;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FaucetController extends Controller
{
    public function index(): Factory|View|Application
    {
        $faucetPrice = 0.001; //USD
        $waxUsdApi = 'https://api.binance.com/api/v3/ticker/price?symbol=WAXPUSDT';
        $waxUsdResponse = Http::get($waxUsdApi)->json();
        $waxUsd = $waxUsdResponse['price'];

        $unlockTimeArray = [];
        $faucets = [];
        $userInfo = session('user', false);
        if ($userInfo) {
            $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);
            $unlockTimeArray = [];
            $faucets = [];
            Faucet::query()->where('active', '=', 1)->each(
                function ($faucet) use ($user, &$unlockTimeArray, &$faucets, $faucetPrice, $waxUsd) {
                    $tokenWax = 1;
                    if ($faucet->alcor_id) {
                        $alcorApiUrl = "https://wax.alcor.exchange/api/markets/$faucet->alcor_id";
                        $alcorApiResponse = Http::get($alcorApiUrl)->json();
                        $tokenWax = $alcorApiResponse['last_price'];
                    }
                    $faucet->counter = round(($faucetPrice / $waxUsd) / $tokenWax, 3);
                    $faucet->save();

                    $faucets[$faucet->getAttributeValue('id')] = $faucet;
                    $lastTransaction = FaucetTransaction::query()
                        ->where('user_id', $user->getAttributeValue('id'))
                        ->where('faucet_id', $faucet->getAttributeValue('id'))
                        ->orderByDesc('created_at')
                        ->first();
                    if ($lastTransaction) {
                        $unlockTime = new Carbon($lastTransaction->getAttributeValue('created_at'));
                        $unlockTime->addSeconds($faucet->timer);
                        $unlockTimeArray[$faucet->id] = $unlockTime;
                    }
                }
            );
        }

        return \view(
            'redesign.pages.faucet',
            [
                'unlockTimes' => $unlockTimeArray,
                'faucets' => $faucets,
            ]
        );
    }


    public function claim(Request $request): JsonResponse
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);

        $faucet = Faucet::query()
            ->where('active', '=', 1)
            ->find($request->get('faucet'));

        $token = $faucet->getAttributeValue('token');
        $count = $faucet->getAttributeValue('counter');
        $timer = $faucet->getAttributeValue('timer');
        $contract = $faucet->getAttributeValue('contract');

        $lastTransaction = FaucetTransaction::query()
            ->where('user_id', $user->getAttributeValue('id'))
            ->where('faucet_id', $faucet->getAttributeValue('id'))
            ->orderByDesc('created_at')
            ->first();

        if ($lastTransaction) {
            $unlockTime = new Carbon($lastTransaction->getAttributeValue('created_at'));
            $unlockTime->addSeconds($timer);
            if (Carbon::now() < $unlockTime) {
                return response()->json(['status' => 'error', 'message' => 'Time limit not over']);
            }
        }

        $kleos = new Kleos();
        $transfer = $kleos->transfer($user['userAccount'], $count, $token, $contract, 'Faucet')['response'];
        if ($transfer->transaction_id) {
            (new FaucetTransaction(['user_id' => $user->id, 'faucet_id' => $faucet->getAttributeValue('id')]))->save();
        }
        return response()->json(['status' => 'success', 'transfer' => $transfer]);
    }
}
