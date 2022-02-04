<?php

namespace App\Console\Commands;

use App\Models\WaxUserModel;
use Illuminate\Console\Command;

class NullifyAni extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ani:nullify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nullify ANI';

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
        foreach (WaxUserModel::all() as $user){
            $user->ANI = 0;
            $user->save();
        }
        return 0;
    }
}
