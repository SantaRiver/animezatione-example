<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaxUserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetPackInformationController extends Controller
{
    public function __invoke($userName, Request $request): JsonResponse
    {
        $user = WaxUserModel::query()->firstWhere('userAccount', $userName);
        if (empty($user)){
            return response()->json(['status' => 'error', 'message' => "User $userName not found"]);
        }
        $user->updateInventory();
        $pack = $user->inventory()->firstWhere('asset_id', $request->get('asset_id'));
        if (empty($user)){
            return response()->json(['status' => 'error', 'message' => "Pack ". $request->get('asset_id') . " not found"]);
        }
        return response()->json(['status' => 'success', 'pack' => $pack->id ]);
    }
}
