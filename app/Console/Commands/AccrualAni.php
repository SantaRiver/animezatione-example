<?php

namespace App\Console\Commands;

use App\Models\Debug;
use App\Models\Log\Schedule;
use App\Models\NFTsModel;
use App\Models\UserStakingRate;
use App\Models\UserWallet;
use App\Models\WaxUserModel;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class AccrualAni extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ani:accrual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accrual AniCoin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $log = [
            'command' => 'ani:accrual',
            'status' => 'start',
        ];
        (new Schedule($log))->save();
        WaxUserModel::query()->each(function ($user){
            $userStakingRate = UserStakingRate::query()->firstWhere('user_id', $user->id);
            if (!$userStakingRate){
                $userStakingRate = new UserStakingRate(['user_id' => $user->id]);
            }
            $userWallet = UserWallet::query()->firstWhere('user_id', $user->id);
            if (!$userWallet){
                $userWallet = new UserWallet(['user_id' => $user->id]);
            }
            $userWallet->ani += $userStakingRate->ani ?? 0;
            $userWallet->save();
        });
        $log['status'] = 'end';
        (new Schedule($log))->save();
        return 1;
    }
}
