<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\AssignPermissionRequest;
use App\Http\Requests\Role\AssignRoleRequest;
use App\Http\Requests\Role\RoleStoreRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Role::all()], 200);
    }

    public function store(RoleStoreRequest $request)
    {
        $data = $request->validated();
        $validPermissions = Permission::query()->whereIn('name', $data['permissions'])->pluck('id');
        if ($validPermissions->count() != count($data['permissions'])) {
            return response()->json(['message' => 'permissions not found'], 422);
        }
        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($validPermissions);
        return response()->json($role->load('permissions'), 201);
    }

    public function show(Role $role)
    {
        return response()->json(['data' => $role], 200);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function assignPermissions(AssignPermissionRequest $request, Role $role): JsonResponse
    {
        $data = $request->validated();
        $role->syncPermissions($data['permissions']);
        return response()->json(['message' => 'Permissions assigned']);
    }

    public function assignRoles(AssignRoleRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        $user->syncRoles($data['roles']);
        return response()->json(['message' => 'Roles assigned']);
    }
}
