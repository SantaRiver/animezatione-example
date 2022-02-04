<?php

namespace App\Console\Commands;

use App\Models\Pool;
use App\Models\WaxUserModel;
use Illuminate\Console\Command;

class WaxLock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wax:lock';

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
        $pool = Pool::query()->first();
        WaxUserModel::all()->each(function ($user) use ($pool){
            $user->locked_wax += round(($pool->amount / 5) * ($user->percent / 100), 2);
            $user->save();
        });
        $pool->amount *= 0.8;
        $pool->save();
        return 0;
    }
}
