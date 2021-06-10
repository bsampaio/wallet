<?php

namespace App\Http\Middleware;

use App\Models\Wallet;
use Closure;
use Illuminate\Http\Request;

class RequestHasValidWalletKey
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
        $key = config('wallet.headers.Wallet-Key');
        if(!$request->hasHeader($key) || !$request->header($key)) {
            return abort(401, "We can't find wallet key information.");
        }

        $walletKey = $request->header($key);
        if(!Wallet::active()->lockedBy($walletKey)->exists()) {
            return abort(401, "There is no wallet with this key.");
        }


        return $next($request);
    }
}
