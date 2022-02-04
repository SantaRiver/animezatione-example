<?php

namespace App\Http\Controllers;

use App\Http\Resources\NFT\NFTResourceCollection;
use App\Models\NFTsModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index()//: Application|Factory|View
    {
        return view('redesign.pages.collection');
    }

    public function ofList(Request $request): Factory|View|Application
    {
        return \view(
            'redesign.pages.cards',
            [
                'collection' => NFTsModel::query()
                        ->where('active', '=', 1)
                        ->whereNotIn('assets', ['pack'])
                        ->where('name', 'like', '%'.$request->get('query', ''))
                        ->orderBy('assets')
                        ->paginate('10'),
            ]
        );
    }
}
