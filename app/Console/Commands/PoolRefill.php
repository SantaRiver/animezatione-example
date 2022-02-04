<?php

namespace App\Console\Commands;

use App\Models\Debug;
use App\Models\Pool;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PoolRefill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pool:refill';

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

    /**na
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $presale = 222;
        $apiUrl = 'https://wax.api.atomicassets.io/atomicassets/v1/templates/animezatione/243530';
        $response = Http::get($apiUrl);
        if ($response->json()['success']) {
            $sold = $response->json()['data']['issued_supply'] - $presale;
            $pool = Pool::query()->first();
            if (is_null($pool)) {
                $pool = new Pool();
            }
            $pool->amount = round(712);
            $pool->save();
        }

        $debug = new Debug(['debug' => 'pool:refill']);
        $debug->save();

        return 0;
    }
}
