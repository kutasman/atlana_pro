<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiRequestLogging
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
        Log::info('Incoming request:');
        Log::info($request);
        return $next($request);
    }

    /**
     * @param Request $request
     * @param JsonResponse $response
     * @return void
     */
    public function terminate(Request $request, JsonResponse $response)
    {
        Log::info('Outgoing response:');
        Log::info($response);
    }
}
