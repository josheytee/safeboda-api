<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonRequestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])
            && $request->isJson()
        ) {

            $data = $request->getContent();
            // dd($data)
            // $request->request->replace(is_array($data) ? $data : []);
            $request->request->replace(json_decode($data, true));
        }

        return $next($request);
    }
}