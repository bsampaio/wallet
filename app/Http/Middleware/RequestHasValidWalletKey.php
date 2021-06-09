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
        if(!$request->hasHeader('wallet_key') || !$request->header('wallet_key')) {
            return abort(401, "We can't find wallet key information.");
        }

        $walletKey = $request->header('wallet_key');
        if(!Wallet::active()->lockedBy($walletKey)->exists()) {
            return abort(401, "There is no wallet with this key.");
        }


        return $next($request);
    }
}
