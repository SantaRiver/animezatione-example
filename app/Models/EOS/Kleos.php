<?php

namespace App\Models\EOS;

use App\Models\PackRewards;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Kleos extends Model
{
    use HasFactory;

    /**
     * @param  string  $recipient
     * @param  float  $quantity
     * @param  string  $token
     * @return array
     */
    public function transfer(
        string $recipient,
        float $quantity,
        string $token,
        string $contract = 'eosio.token',
        string $memo = ''
    ): array {
        if ($token == 'WAX') {
            $quantity = number_format($quantity, 8);
        }

        $this->unlockWallet();
        return $this->execWithInfo(
            [
                'cleos -u https://wax.greymass.com/ push action '.$contract.' transfer \'["anionereward", "'.
                $recipient.'", "'.
                $quantity.' '.
                $token.'", "'.
                $memo.'" ]\' -p anionereward@active -j'
            ],
            false,
            true
        )[0];
    }


    public function unlockWallet()
    {
        $this->execWithInfo(
            [
                //'cleos wallet create --to-console --name wallet',
                'cleos wallet open --name wallet',
                'cleos wallet unlock --name wallet --password секрет',
            ]
        );
    }

    public function execWithInfo($commands, $debug = false, $merger = false): array
    {
        $result = [];
        foreach ($commands as $command) {
            $status = null;
            $response = null;
            $commandResult = '';
            exec($command.' 2>&1', $response, $status);
            if (count($response) > 1) {
                foreach ($response as $line) {
                    $commandResult .= $line;
                }
                $commandResult = json_decode($commandResult);
            } else {
                $commandResult = $response[0] ?? '';
            }
            $result[] = [
                'command' => $command,
                'status' => $status,
                'response' => ($merger) ? $commandResult : $response,
            ];
        }
        if ($debug) {
            dd($result);
        }
        return $result;
    }

    function transferAssets($user, $asset_ids)
    {
        $assetsIdQuery = '['.implode(', ', $asset_ids).']';
        $this->unlockWallet();
        return $this->execWithInfo(
            [
                'cleos -u https://wax.greymass.com/ push transaction -j \'{
                "actions":[{
                    "account":"atomicassets",
                    "name":"transfer",
                    "data":{
                        "from":"anionereward",
                        "to":"'.$user.'",
                        "asset_ids":'.$assetsIdQuery.',
                        "memo":""},
                    "authorization":[{
                        "actor":"anionereward",
                        "permission":"active"}
                    ]}
                ]
            }\''
            ],
            false,
            true
        )[0];
    }

    public function claim()
    {
    }

    public function unpack(string $packId)
    {
        PackRewards::getChance($packId);
    }

    public function account($wallet = 'anionereward')
    {
        $command = 'cleos -u https://wax.greymass.com/ get account '.$wallet.' -j';
        return $this->execWithInfo([$command], false, true)[0]['response'];
    }

    public function getTransaction($id)
    {
        $url = "https://wax.greymass.com/v1/history/get_transaction?id=$id";
        return Http::get($url)->json();
    }

    public function getBalance($contract, $account, $token): string
    {
        $command = "cleos -u https://wax.greymass.com/ get currency balance $contract $account $token";
        $rowResult = $this->execWithInfo([$command], false, true)[0]['response'];
        return explode(' ', $rowResult)[0];
    }
}
