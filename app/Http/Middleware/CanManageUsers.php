<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanManageUsers
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->canManageUsers()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No tienes permisos para gestionar usuarios y grupos.');
        }

        return $next($request);
    }
}
