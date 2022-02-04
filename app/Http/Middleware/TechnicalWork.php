<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TechnicalWork
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->ip(),['95.105.113.135', '46.191.138.22', '194.87.186.52'])) {
            return $next($request);
        }
        return response()->view('redesign.pages.coming-soon');
    }
}
