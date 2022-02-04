<?php

namespace App\Http\Controllers;

use App\Models\Pool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PoolController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $pool = Pool::query()->first();
        $pool->amount = $request->get('pool');
        $pool->save();

        return redirect()->back();
    }
}
