<?php

namespace App\Http\Controllers;

use App\Http\Requests\WaxUserRequest;
use App\Models\WaxUserModel;
use Illuminate\Http\JsonResponse;

class WaxAuthController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  WaxUserRequest  $request
     * @return JsonResponse
     */
    public function login(WaxUserRequest $request): JsonResponse
    {
        $waxUser = WaxUserModel::query()->firstWhere('userAccount', '=', $request->get('userAccount'));
        if ($waxUser){
            WaxUserModel::query()->find($waxUser['id'])->update(
                [
                    'pubKey' => $request->get('pubKey'),
                    'secureKey' => $request->get('secureKey'),
                    'permission' => $request->get('permission'),
                ]
            );
        }
        if (!$waxUser) {
            $waxUser = new WaxUserModel($request->validated());
            $waxUser->save();
        }
        session()->put('user', $request->all());
        return response()->json(['status' => 'success', 'user' => $waxUser]);
    }

    public function logout(){
        session()->put('user', false);
        return redirect('/');
    }
}
