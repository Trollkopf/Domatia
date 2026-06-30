<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureBackofficePermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $user = auth()->user();

        $authorized = match ($permission) {
            'properties' => $user->canManageProperties(),
            'publish_properties' => $user->canPublishProperties(),
            'contacts' => $user->canManageContacts(),
            'zonas' => $user->canManageZonas(),
            'reports' => $user->canViewReports(),
            'export_reports' => $user->canExportReports(),
            default => false,
        };

        if (! $authorized) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
