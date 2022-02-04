<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaxUserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetUserBalanceController extends Controller
{
    /**
     * @param $userName
     * @return JsonResponse
     */
    public function getBalance($userName): JsonResponse
    {
        $user = WaxUserModel::query()->firstWhere('userAccount', $userName);
        if (empty($user)){
            return response()->json(['status' => 'error', 'message' => "user $userName not found"]);
        }
        return response()->json(['status' => 'success', 'balance' => $user->locked_wax]);
    }

}
