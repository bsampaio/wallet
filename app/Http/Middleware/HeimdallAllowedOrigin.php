<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HeimdallAllowedOrigin
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
        if(in_array(env('APP_ENV'), ['local', 'staging', 'development'])) {
            return $next($request);
        }

        //TODO: Filter origin access
        $apps = config('heimdall.allowed');

        return abort(403);
    }
}
