<?php

namespace App\Console\Commands;

use App\Models\Debug;
use App\Models\PackModel;
use App\Models\WaxUserModel;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class ParsePack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:pack';

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
        $debug = new Debug(['debug' => 'parse:pack start']);
        $debug->save();
        $params = [
            'collection_name' => 'animezatione',
            'schema_name' => 'pack',
            'page' => '1',
            'limit' => '100',
            'order' => 'desc',
            'sort' => 'created',
        ];
        $apiUrl = 'https://wax.api.atomicassets.io/atomicassets/v1/templates?'.http_build_query($params);
        $response = Http::get($apiUrl);
        if ($response->json()['success']) {
            $packs = $response->json()['data'];
            $params['template_id'] = '';
            foreach ($packs as $pack) {
                $params['template_id'] = $pack['template_id'];
                $ownersPackUrl = 'https://wax.api.atomicassets.io/atomicassets/v1/accounts?'.http_build_query($params);
                $ownersResponse = Http::get($ownersPackUrl);
                if ($ownersResponse->json()['success']) {
                    $ownersPack = $ownersResponse->json()['data'];
                    foreach ($ownersPack as $owner) {
                        $user = WaxUserModel::query()->where('userAccount', $owner['account'])->first();
                        if ($user){
                            $packCheck = PackModel::query()->where('user_id', $user->id)->first();
                            if (empty($packCheck)){
                                $packRequest = new PackModel(
                                    [
                                        'user_id' => $user->id,
                                        'template_id' => $pack['template_id'],
                                        'name' => $pack['immutable_data']['name'],
                                        'count' => $owner['assets'],
                                    ]
                                );
                                $packRequest->save();
                            } elseif ($owner['assets'] != $packCheck->count){
                                $packCheck->update(['count' => $owner['assets']]);
                            }
                        }
                    }
                    $debug = new Debug(['debug' => 'parse:pack end']);
                    $debug->save();
                    return 0;
                }
            }
            $debug = new Debug(['debug' => 'parse:pack; error:accounts error']);
            $debug->save();
            return 0;
        }
        $debug = new Debug(['debug' => 'parse:pack; error:template error']);
        $debug->save();
        return 0;
    }
}
