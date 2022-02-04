<?php

namespace App\Console\Commands;

use App\Models\CardsMarket;
use App\Models\NFTMarket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PriceRecalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:recalculation';

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
        $WAXUSDT = Http::get("https://wax.alcor.exchange/api/markets/39")->json()['last_price'];
        $ANIWAX = Http::get("https://wax.alcor.exchange/api/markets/143")->json()['last_price'];
        NFTMarket::query()->each(
            function ($cardMarket) use ($WAXUSDT, $ANIWAX) {
                //$cardMarket->price_ani = ceil(($cardMarket->price_usd / $WAXUSDT) / $ANIWAX);
                $cardMarket->price_ani = $cardMarket->price_usd;
                $cardMarket->save();
            }
        );
        return 0;
    }
}
