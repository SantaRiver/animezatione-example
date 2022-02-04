<?php

namespace App\Http\Controllers;

use App\Models\NFTMarket;
use App\Models\NFTsModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ParseCollectionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return Application|RedirectResponse|Redirector
     */
    public function __invoke(Request $request)//: Application|RedirectResponse|Redirector
    {
        $params = [
            'collection_name' => 'animezatione',
            'page' => '1',
            'limit' => '1000',
            'order' => 'desc',
            'sort' => 'created',
        ];
        $apiUrl = 'https://wax.api.atomicassets.io/atomicassets/v1/templates?'.http_build_query($params);
        $response = Http::get($apiUrl);
        if ($response->json()['success']) {
            $responseData = $response->json()['data'];
            foreach ($responseData as $item) {
                if (empty(NFTsModel::query()->where('template_id', $item['template_id'])->first())) {
                    if (isset($item['immutable_data']['img'])) {
                        $imgCode = $item['immutable_data']['img'];
                    } else {
                        $imgCode = $item['immutable_data']['backimg'];
                    }
                    $imgUrl = 'https://gateway.pinata.cloud/ipfs/'.$imgCode;
                    $responseImg = Http::get($imgUrl);
                    $extension = explode('/', $responseImg->header('Content-Type'))[1];
                    $fileName = $imgCode.'.'.$extension;
                    $filePreviewName = $imgCode.'_preview.'.$extension;

                    if (!Storage::disk('public')->exists('cards/'.$fileName)) {
                        Storage::disk('public')->put('cards/'.$fileName, $responseImg);
                        $path = Storage::disk('public')->path('cards/'.$fileName);
                        $img = Image::make($path);
                        $img->resize(round($img->getWidth() / 5), round($img->getHeight() / 5));
                        $img->save($img->dirname.'/'.$filePreviewName);
                    }

                    $nft = new NFTsModel(
                        [
                            'template_id' => $item['template_id'],
                            'name' => $item['immutable_data']['name'],
                            'rarity' => $item['immutable_data']['rarity'] ?? null,
                            'description' => $item['immutable_data']['description'] ?? $item['immutable_data']['Description'] ?? null,
                            'assets' => $item['schema']['schema_name'] ?? null,
                            'path' => $fileName,
                            'preview' => $filePreviewName,
                            'active' => 0,
                        ]
                    );
                    $nft->save();

                } else {
                    $card = NFTsModel::query()->firstWhere('template_id', $item['template_id']);
                    $card->description = $item['immutable_data']['description'] ?? $item['immutable_data']['Description'] ?? null;
                    $card->save();
                }
            }
        }
        //return redirect('/dashboard');
    }
}
