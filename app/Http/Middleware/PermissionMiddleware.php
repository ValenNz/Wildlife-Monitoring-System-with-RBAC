<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->role_id) {
            abort(403, 'User has no role assigned.');
        }

        $hasPermission = DB::table('role_permission')
            ->join('permissions', 'role_permission.permission_id', '=', 'permissions.id')
            ->where('role_permission.role_id', $user->role_id)
            ->where('permissions.name', $permission)
            ->exists();

        if (!$hasPermission) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
