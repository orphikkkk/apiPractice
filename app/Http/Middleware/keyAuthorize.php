<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class keyAuthorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(isset(getallheaders()['API_KEY']) && getallheaders()['API_KEY']=="BA673A414C3B44C98478BB5CF10A0F832574090C") {
            return $next($request);
        }else{
            return response()->json(['success'=>false,'error'=>"Invalid request"], 503);
        }
    }
}
