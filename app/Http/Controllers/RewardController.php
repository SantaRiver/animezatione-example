<?php

namespace App\Http\Controllers;

use App\Http\Resources\RewardResourceCollection;
use App\Models\NFTsModel;
use App\Models\RewardsLog;
use App\Models\WaxUserModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    /**
     * @return View|Factory|Application
     */
    public function index(): View|Factory|Application
    {
        $RewardsLog = RewardsLog::all();
        $rewardsNFT = [];
        foreach ($RewardsLog as $RewardLog) {
            $reward = json_decode($RewardLog->reward);
            $cards = [];
            foreach ($reward as $id){
                $cards[] = NFTsModel::query()->find($id)->toArray();
            }
            $rewardsNFT[] = [
                'cards' => $cards,
                'info' => [
                    'user' => WaxUserModel::query()->find($RewardLog['user_id']),
                    'packTemplateId' => $RewardLog['pack_template_id'],
                    'logId' => $RewardLog['id'],
                ]
            ];
        }

        return view(
            'reward.index',
            [
                'packRewards' => $rewardsNFT
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
     * @param  Request  $request
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
