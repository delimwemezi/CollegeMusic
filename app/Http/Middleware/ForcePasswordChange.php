<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user()?->must_change_password
            && !$request->routeIs('admin.password.force', 'admin.password.update', 'logout')
        ) {
            return redirect()->route('admin.password.force');
        }

        return $next($request);
    }
}