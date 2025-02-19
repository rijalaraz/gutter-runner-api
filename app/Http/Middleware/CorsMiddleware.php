<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
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
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400', // or '1728000'
            'Access-Control-Allow-Headers'     => 'DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range',
            'Access-Control-Expose-Headers'    => 'Content-Length,Content-Range',
        ];

       if ($request->isMethod('OPTIONS'))
       {
           return response()->json('{"method":"OPTIONS"}', 200, $headers);
       }

       $response = $next($request);
       foreach($headers as $key => $value)
       {
           $response->header($key, $value);
       }

       return $response;
    }
}