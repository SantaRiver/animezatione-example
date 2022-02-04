<?php

namespace App\Http\Controllers\Staking;

use App\Http\Controllers\Controller;
use App\Models\WaxUserModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StakingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);
        $user->updateInventory();
        return \view(
            'redesign.pages.staking',
            [
                'ani' => [
                    'perHour' => $user->stakingRate()->first()->ani,
                    'total' => $user->wallet()->first()->ani,
                ]
            ]
        );
    }

    public function inventory(Request $request): View|Factory|Application
    {
        $userInfo = session('user', []);
        $user = WaxUserModel::query()->firstWhere('userAccount', '=', $userInfo['userAccount']);
        return \view(
            'redesign.pages.cards',
            [
                'collection' => $user->inventory()
                    ->join('nft', 'users_inventory.card_id', '=', 'nft.id')
                    ->where('name', 'like', '%'.$request->get('query', ''))
                    ->where('active', '=', true)
                    ->whereNotIn('assets', ['pack'])
                    ->orderBy('assets')
                    ->paginate('10'),
            ]
        );
    }

}
