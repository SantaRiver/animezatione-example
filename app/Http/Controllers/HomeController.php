<?php

namespace App\Http\Controllers;

use App\Http\Resources\WaxUserResourceCollection;
use App\Models\Pool;
use App\Models\UserWallet;
use App\Models\WaxUserModel;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $pool = Pool::all()->first()->amount;
        $totalAni = UserWallet::all()->sum('ani');
        $user = session('user', false);
        $AniUsers = new WaxUserResourceCollection(
            WaxUserModel::query()
                /*->limit(150)*/
                ->orderByDesc('ANI')
                ->get()
        );
        return view(
            'redesign.pages.home',
            [
                'user' => $user,
                'AniUsers' => $AniUsers,
                'pool' => $pool,
                'totalAni' => $totalAni,
            ]
        );
    }

}
