<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HeimdallCheckAppKey
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
        $apps = config('heimdall.allowed');
        $key = config('heimdall.headers.APP_KEY');
        $allowed = false;
        foreach($apps as $app) {
            $allowed = $allowed || $app['key'] === $request->header($key);
        }
        if($allowed) {
            return $next($request);
        }

        return abort(403,'Your app has no authorization to consume this api.');
    }
}
