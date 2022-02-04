<?php

namespace App\Http\Controllers;

use App\Models\EOS\Kleos;
use App\Models\UserTransactions;
use App\Models\UserWallet;
use App\Models\WaxUserModel;
use Carbon\Carbon;
use Carbon\Traits\Creator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EOSController extends Controller
{
    public function claim(): JsonResponse
    {

        $user = session('user', false);
        $user = WaxUserModel::query()->firstWhere('userAccount', $user['userAccount']);
        $userTransaction = UserTransactions::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!empty($userTransaction)) {
            $lastClaim = new Carbon($userTransaction->created_at);
            $lastClaimTomorrow = $lastClaim->addDay();
            if ($lastClaimTomorrow > Carbon::now()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => '1 day have not passed since the last withdrawal'
                    ]
                );
            }
        }
        $userWallet = UserWallet::query()->firstWhere('user_id', $user->id);
        if ($userWallet->ani == 0) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Your wallet balance is zero'
                ]
            );
        }
        if ($userWallet->ani < 1) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Your wallet balance less then 1 ANI'
                ]
            );
        }
        $kleos = new Kleos();
        $transfer = $kleos->transfer($user['userAccount'], floor($userWallet->ani), 'ANI', 'anionereward', 'Claim');
        if ($transfer['response']->transaction_id) {
            (new UserTransactions(
                [
                    'user_id' => $user->id,
                    'action' => 'claim',
                    'value' => floor($userWallet->ani)
                ]
            ))->save();
            $userWallet->ani = $userWallet->ani - floor($userWallet->ani);
            $userWallet->save();
            return response()->json(['status' => 'success', 'transaction_id' => $transfer['response']->transaction_id]);
        }
        (new UserTransactions(
            [
                'user_id' => $user->id,
                'action' => 'error',
                'value' => $userWallet->ani,
            ]
        ))->save();
        return response()->json(['status' => 'error', 'message' => 'Error while translating tokens']);
    }
}
