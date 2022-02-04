<?php

namespace App\Console\Commands;

use App\Models\Cards;
use App\Models\Log\Schedule;
use App\Models\NFTsModel;
use App\Models\UserInventory;
use App\Models\UserPoolRattitude;
use App\Models\UserStakingRate;
use App\Models\UserWallet;
use App\Models\WaxUserModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class InventoryUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            'command' => 'inventory:update',
            'status' => 'start',
        ];
        (new Schedule($log))->save();
        WaxUserModel::query()->each(function ($user){
            $user->updateInventory();
        });
        $log['status'] = 'end';
        (new Schedule($log))->save();
        return 0;
    }
}
