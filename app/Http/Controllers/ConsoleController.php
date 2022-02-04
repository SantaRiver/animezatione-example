<?php

namespace App\Http\Controllers;

use App\Models\ActiveStacking;
use App\Models\EOS\Kleos;
use App\Models\NFTMarket;
use App\Models\NFTsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class ConsoleController extends Controller
{
    public function index()
    {
        $kleos = new Kleos();
        echo '<pre>';
        print_r($kleos->getBalance('anionereward', 'fflro.wam', 'ANI'));
        echo '</pre>';
    }

    public function santa()
    {
        $FWW = Http::get("https://wax.alcor.exchange/api/markets/104")->json()['last_price'];
        $FWF = Http::get("https://wax.alcor.exchange/api/markets/105")->json()['last_price'];
        $FWG = Http::get("https://wax.alcor.exchange/api/markets/106")->json()['last_price'];
        $farmerToken = [
            'wood' => $FWW,
            'food' => $FWF,
            'gold' => $FWG,
        ];
        $resource = [
            'gold' => 0,
            'wood' => 0,
            'food' => 0,
            'energy' => 200,
        ];

        $items = [
            'Axe' => [
                'reward' => 'wood',
                'rewardRate' => 1.7,
                'chargeTime' => 3600,
                'energyConsumed' => 5,
                'durabilityConsumed' => 3,
            ]
        ];

        $profit = $resource;
        foreach ($items as $item => $itemStat) {
            for ($strength = 100; $strength > $itemStat['durabilityConsumed']; $strength -= $itemStat['durabilityConsumed']) {
                $profit[$itemStat['reward']] += $itemStat['rewardRate'];
                $profit['energy'] -= $itemStat['energyConsumed'];
            }
        }
        $waxProfit = [];
        foreach ($profit as $resName => $res) {
            if ($resName != 'energy') {
                $waxProfit[$resName] = $res * $farmerToken[$resName];
            }
        }

        dd($waxProfit);
    }

    public function nansen()
    {
        $res = Http::get('https://pro.nansen.ai/nft-paradise/mint');
        echo '<pre>';
        var_dump($res);
        echo '</pre>';
    }
}
