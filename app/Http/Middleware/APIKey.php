<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APIKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('k')) {
            $received = $request->query('k'); // k = hash
            // recreate hash so we can compare with k
            $rhash = hash('murmur3c', env('APPROVERS_API_KEY') . $request->query('action') . 'salt and pepper', false);

            if ($received == $rhash) return $next($request);
        }
    }
}
