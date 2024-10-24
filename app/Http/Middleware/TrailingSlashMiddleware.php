<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrailingSlashMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = $request->getRequestUri();

        // Check if the URL does not have a query string and does not end with a slash
        if (!preg_match('/\/$/', $uri) && !preg_match('/\.\w+$/', $uri)) {
            // Redirect to the URL with a trailing slash
            return redirect($uri . '/');
        }

        return $next($request);
    }
}
