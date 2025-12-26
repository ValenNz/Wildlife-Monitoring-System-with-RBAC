<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->role_id) {
            abort(403, 'No role assigned');
        }

        $hasPermission = DB::table('role_permission')
            ->join('permissions', 'role_permission.permission_id', '=', 'permissions.id')
            ->where('role_permission.role_id', $user->role_id)
            ->where('permissions.name', $permission)
            ->exists();

        if (!$hasPermission) {
            abort(403, 'Permission denied');
        }

        return $next($request);
    }
}
