<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SubscriptionController extends Controller
{
    public function index(): string
    {
        $params = [
            'collection_name' => 'animezatione',
            'schema_name' => 'subscription',
            'template_id' => '243530',
            'page' => '1',
            'limit' => '1000',
            'order' => 'desc',
            'sort' => 'asset_id',
        ];
        $assetsUrl = 'https://wax.api.atomicassets.io/atomicassets/v1/assets?'.http_build_query($params);
        $assetsResponse = Http::get($assetsUrl)->json();
        $owners = [];
        if ($assetsResponse['success']){
            foreach ($assetsResponse['data'] as $asset){
                $owners[] = $asset['owner'];
            }
        }
        $owners = array_unique($owners);
        echo 'Колличество: ' . sizeof($owners) . '<br>' . '<br>';
        foreach ($owners as $owner){
            echo $owner . '<br>';
        }
        return '';
    }
}
