<?php

namespace App\Console\Commands;

use App\Models\BurnLog;
use App\Models\Debug;
use App\Models\NFTsModel;
use App\Models\WaxUserModel;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class BurnAni extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ani:burn';

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
        $debug = new Debug(['debug' => 'ani:burn start']);
        $debug->save();
        $users = WaxUserModel::all();
        $user = $users->pop();
        while ($user != $users->last()) {
            $aniToBurn = 0;
            $burnUrl = 'https://wax.api.atomicassets.io/atomicassets/v1/burns/'.$user->userAccount.'?collection_whitelist=animezatione';
            $burnResponse = Http::get($burnUrl);
            if ($burnResponse->json()['success']) {
                if ($burnResponse->json()['data']['assets']) {
                    $counter = [];
                    foreach ($burnResponse->json()['data']['templates'] as $template) {
                        $counter[$template['template_id']] = (int)($template['assets']);
                    }
                    $animezationeInventory = NFTsModel::query()->whereIn('template_id', array_keys($counter))->get();
                    foreach ($animezationeInventory as $animezationeItem) {
                        $burnLogCheck = BurnLog::query()
                            ->where('user_id', $user->id)
                            ->where('nft_id', $animezationeItem->id)
                            ->first();
                        if (empty($burnLogCheck)) {
                            $burnLog = new BurnLog(
                                [
                                    'user_id' => $user->id,
                                    'nft_id' => $animezationeItem->id,
                                    'count' => $counter[$animezationeItem->template_id]
                                ]
                            );
                            $burnLog->save();
                            $aniToBurn += $animezationeItem->burning * $counter[$animezationeItem->template_id];
                        } elseif ($burnLogCheck->count != $counter[$animezationeItem->template_id]) {
                            $aniToBurn += $animezationeItem->burning *
                                ($counter[$animezationeItem->template_id] - $burnLogCheck->count);

                            $burnLogCheck->count = $counter[$animezationeItem->template_id];
                            $burnLogCheck->save();
                        }
                    }
                    $user->ANI += $aniToBurn;
                    $user->save();
                }
                $user = $users->pop();
            } else {
                sleep(10);
            }
        }
        $debug = new Debug(['debug' => 'ani:burn end']);
        $debug->save();
        return 0;
    }
}
