<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanManageSettings
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->canManageSettings()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No tienes permisos para gestionar los ajustes del sitio.');
        }

        return $next($request);
    }
}
