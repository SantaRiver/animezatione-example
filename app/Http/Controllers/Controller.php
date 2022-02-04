<?php

namespace App\Http\Controllers;

use App\Models\UserTransactions;
use App\Models\WaxUserModel;
use Carbon\Carbon;
use Carbon\Traits\Creator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $user = session('user', false);
        if ($user){
            $aniUser = WaxUserModel::query()->firstWhere('userAccount', $user['userAccount']);
            View::share('aniUser', $aniUser);
            $userWallet = $aniUser->wallet()->first();
            View::share('userWallet', $userWallet);
            $unlockClaim = true;
            if ($userWallet->ani < 1){
                $unlockClaim = false;
            }
            $userTransaction = UserTransactions::query()
                ->where('user_id', $aniUser->id)
                ->orderBy('created_at', 'desc')
                ->first();
            if (!empty($userTransaction)) {
                $lastClaim = new Carbon($userTransaction->created_at);
                $lastClaimTomorrow = $lastClaim->addDay();
                if ($lastClaimTomorrow > Carbon::now()) {
                    $unlockClaim = false;
                }
            }
            View::share('unlockClaim', $unlockClaim);
        }
        View::share('user', $user);
    }
}
