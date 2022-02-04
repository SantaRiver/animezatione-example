<?php

namespace App\Http\Controllers;

use App\Http\Resources\WaxUserResourceCollection;
use App\Models\NFTMarket;
use App\Models\NFTsModel;
use App\Models\Pool;
use App\Models\WaxUserModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminPanelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $NFTResourceCollection = NFTsModel::query()
            ->get();
        foreach ($NFTResourceCollection as $card) {
            $market = NFTMarket::query()->firstWhere(
                ['nft_id' => $card['id']]
            );
            if (empty($market)) {
                $market = new NFTMarket(
                    ['nft_id' => $card['id'], 'price_usd' => 0, 'price_ani' => 0]
                );
                $market->save();
            }
        }
        $NFTResourceCollection = NFTsModel::query()
            ->join('nft_markets', 'nft.id', '=', 'nft_markets.nft_id')
            ->get();
        $WaxUserResourceCollection = new WaxUserResourceCollection(WaxUserModel::all());
        $pool = Pool::all()->first()->amount;
        return view(
            'dashboard',
            [
                'NFTResourceCollection' => $NFTResourceCollection,
                'WaxUserResourceCollection' => $WaxUserResourceCollection,
                'pool' => $pool,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
