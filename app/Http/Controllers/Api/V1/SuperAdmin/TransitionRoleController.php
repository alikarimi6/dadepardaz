<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransitionRoleController extends Controller
{

    public function sync(Request $request, Transition $transition): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => 'exists:roles,id',
        ]);

        $transition->roles()->sync($validated['role_id']);
        return response()->json(['message' => 'Roles synced']);
    }
    public function list(Transition $transition)
    {
//        todo : show role name
        return response()->json($transition->roles);
    }

    public function index(Transition $transition)
    {
        return response()->json([
            'roles' => $transition->roles()->get()
        ]);
    }
    public function store(Request $request, Transition $transition)
    {
        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        $transition->roles()->sync($request->role_ids);

        return response()->json([
            'message' => 'Roles assigned to transition successfully.',
            'roles' => $transition->roles
        ]);
    }
    public function attach(Request $request, Transition $transition)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $transition->roles()->attach($request->role_id);

        return response()->json([
            'message' => 'Role attached to transition successfully.'
        ]);
    }
    public function detach(Request $request, Transition $transition): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $transition->roles()->detach($request->role_id);

        return response()->json([
            'message' => 'Role detached from transition successfully.'
        ]);
    }

}
