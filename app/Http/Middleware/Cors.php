<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Cors
{
    public function handle(Request $request, \Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin'   =>  $request->server('HTTP_ORIGIN') ?: '*',
            'Access-Control-Allow-Headers'  =>  'Content-Type, Authorization, Origin',
            'Access-Control-Allow-Methods'  =>  'GET, POST, DELETE, PUT, OPTIONS'
        ];
        if ($request->isMethod('options')) {
            return response()->make('', 200, $headers);
        }
        $response = $next($request);
        if ($response instanceof Response) {
            return $response->withHeaders($headers);
        }

        return $response;
    }
}
