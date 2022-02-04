<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NFTsModel;
use App\Models\PackModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetPackIdController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param $templateId
     * @return JsonResponse
     */
    public function __invoke($templateId): JsonResponse
    {
        $card = NFTsModel::query()
            ->where('assets', 'pack')
            ->firstWhere('template_id', $templateId);

        return response()->json(['status' => 'success', 'packId' => $card->id]);
    }
}
