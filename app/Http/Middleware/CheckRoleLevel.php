<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleLevel
{
    public function handle(Request $request, Closure $next, string $roleName): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Log in first.');
        }

        $user = Auth::user();

        if (!$user->role) {
            return redirect()->route('login')->with('error', 'Your profile does not have permission.');
        }

        $requiredRole = Role::where('name', $roleName)->first();

        if (!$requiredRole) {
            abort(403, 'Role does not exist');
        }

        if ($user->role->level > $requiredRole->level) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Profile required:' . $requiredRole->name);
        }

        return $next($request);
    }
}
