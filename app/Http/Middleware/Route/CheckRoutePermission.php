<?php

namespace App\Http\Middleware\Route;

use App\Models\RoutePermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckRoutePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $routeName = Route::currentRouteName();

        if ($user->hasRole('superadmin')) {
            return $next($request);
        }

        $permissionName = RoutePermission::where('route_name', $routeName)->value('permission_name');

        if ($permissionName && !$user->can($permissionName)) {
            return response()->json(['error' =>'you are not allowed to perform this action.'] , 403,);
        }

        return $next($request);    }
}
