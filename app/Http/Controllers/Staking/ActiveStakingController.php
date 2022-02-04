<?php

namespace App\Http\Controllers\Staking;

use App\Http\Controllers\Controller;
use App\Models\ActiveStacking;
use App\Models\EOS\Kleos;
use App\Models\Log\ActiveStackingTransaction;
use App\Models\WaxUserModel;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ActiveStakingController extends Controller
{
    public function index(): Factory|View|Application
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);
        $claimTokens = ActiveStacking::query()
            ->where('status', '=', 'claim')
            ->sum('reward');
        $remainingTokens = 1000000 - $claimTokens;
        $stackedTokens = ActiveStacking::query()
            ->where('status', '=', 'stacking')
            ->sum('value');
        $userTransaction = ActiveStacking::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $userStackedTokens = ActiveStacking::query()
            ->where('user_id', $user->id)
            ->where('status', '=', 'stacking')
            ->sum('value');

        $userTransaction->where('status', '=', 'stacking')
            ->each(
                function ($transaction) {
                    if ($transaction['end_time'] < Carbon::now()) {
                        $transaction['status'] = 'claimable';
                        $transaction->save();
                    }
                }
            );

        $kleos = new Kleos();
        $userAniAmount = $kleos->getBalance('anionereward', $user['userAccount'], 'ANI');

        return \view(
            'redesign.pages.active-staking',
            [
                'stakingPool' => [
                    'remainingTokens' => $remainingTokens,
                    'stackedTokens' => $stackedTokens,
                ],
                'staking' => [
                    'userAniAmount' => $userAniAmount,
                    'userStackedTokens' => $userStackedTokens
                ],
                'transactions' => $userTransaction,
            ]
        );
    }

    public function stack(Request $request): JsonResponse
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);
        $request->validate(['transaction_id' => 'string|required']);

        $url = 'https://wax.greymass.com/v1/history/get_transaction?id='.$request->get('transaction_id');
        $response['error'] = '';
        while (isset($response['error'])) {
            $response = Http::get($url)->json();
        }

        $act = $response['trx']['trx']['actions'][0];
        if ($act['account'] != 'anionereward' ||
            $act['data']['from'] != $user['userAccount'] ||
            $act['data']['to'] != 'anionereward') {
            return response()->json(
                ['status' => 'error', 'message' => 'Unable to establish the authenticity of the transaction']
            );
        }
        $amountToken = explode(' ', $act['data']['quantity'])[0];
        $now = Carbon::now();
        $nextMonth = $now->addMonth();
        $activeStacking = new ActiveStacking(
            [
                'user_id' => $user['id'],
                'status' => 'stacking',
                'value' => $amountToken,
                'reward' => round($amountToken * 1.2),
                'end_time' => $nextMonth
            ]
        );
        $activeStacking->save();
        (new ActiveStackingTransaction(
            [
                'user_id' => $user['id'],
                'active_stacking_id' => $activeStacking['id'],
                'transaction_id' => $request->get('transaction_id'),
                'status' => 'stack',
            ]
        ))->save();
        return response()->json(
            ['status' => 'success']
        );
    }

    public function cancel(Request $request): JsonResponse
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);
        $activeStackingId = $request->get('active_stacking_id');
        $activeStacking = ActiveStacking::query()
            ->where('user_id', $user['id'])
            ->where('status', '=', 'stacking')
            ->find($activeStackingId);
        if (empty($activeStacking)) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Could not find staking record for this user'
                ]
            );
        }
        $createdAt = Carbon::createFromFormat('Y-m-d H:i:s', $activeStacking['created_at']);
        $createdAt->addDays(1);
        $now = Carbon::now();
        if ($now < $createdAt) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Staking can only be canceled after 24 hours'
                ]
            );
        }

        $kleos = new Kleos();
        $transfer = $kleos->transfer(
            $user['userAccount'],
            intval($activeStacking['value']),
            'ANI',
            'anionereward',
            'Resume'
        );
        if ($transfer['response']->transaction_id) {
            $activeStacking['status'] = 'cancel';
            $activeStacking->save();
            (new ActiveStackingTransaction(
                [
                    'user_id' => $user['id'],
                    'active_stacking_id' => $activeStackingId,
                    'transaction_id' => $transfer['response']->transaction_id,
                    'status' => 'resume',
                ]
            ))->save();
            return response()->json(
                [
                    'status' => 'success',
                    'transaction_id' => $transfer['response']->transaction_id
                ]
            );
        }
        return response()->json(
            [
                'status' => 'error',
                'transaction_id' => 'An error occurred while canceling'
            ]
        );
    }

    public function claim(Request $request): JsonResponse
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);
        $activeStackingId = $request->get('active_stacking_id');
        $activeStacking = ActiveStacking::query()
            ->where('user_id', $user['id'])
            ->where('status', '=', 'claimable')
            ->find($activeStackingId);
        if (empty($activeStacking)) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Could not find staking record for this user'
                ]
            );
        }

        $kleos = new Kleos();
        $transfer = $kleos->transfer(
            $user['userAccount'],
            intval($activeStacking['reward']),
            'ANI',
            'anionereward',
            'Claim'
        );

        if ($transfer['response']->transaction_id) {
            $activeStacking['status'] = 'claim';
            $activeStacking->save();
            (new ActiveStackingTransaction(
                [
                    'user_id' => $user['id'],
                    'active_stacking_id' => $activeStackingId,
                    'transaction_id' => $transfer['response']->transaction_id,
                    'status' => 'claim',
                ]
            ))->save();
            return response()->json(
                [
                    'status' => 'success',
                    'transaction_id' => $transfer['response']->transaction_id
                ]
            );
        }
        return response()->json(
            [
                'status' => 'error',
                'transaction_id' => 'An error occurred while claiming'
            ]
        );
    }
}
