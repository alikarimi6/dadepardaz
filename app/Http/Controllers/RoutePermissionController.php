<?php

namespace App\Http\Controllers;

use App\Http\Requests\Route\RoutePermissionStoreRequest;
use App\Models\RoutePermission;
use Illuminate\Http\Request;

class RoutePermissionController extends Controller
{
    public function store(RoutePermissionStoreRequest $request)
    {
        $data = $request->validated();
        $routePermission = RoutePermission::create($data);
        return response()->json(['routePermission' => $routePermission] , 201);
    }
}
