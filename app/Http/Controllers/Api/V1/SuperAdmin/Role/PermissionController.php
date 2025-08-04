<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return response()->json(Permission::all());
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:permissions']);
        $permission = Permission::create(['name' => $request->name]);
        return response()->json($permission, 201);
    }

    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate(['name' => 'required|string|unique:permissions,name,' . $permission->id]);
        $permission->update(['name' => $request->name]);
        return response()->json($permission);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['message' => 'Permission deleted']);
    }
}
