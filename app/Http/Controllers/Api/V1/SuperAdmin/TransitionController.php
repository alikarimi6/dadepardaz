<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transition\TransitionStoreRequest;
use App\Models\Transition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Transition::with(['fromState', 'toState', 'roles'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransitionStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $transition = Transition::query()->create($data);
        return response()->json($transition, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transition $transition): JsonResponse
    {
        return response()->json($transition->load(['fromState', 'toState', 'roles']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transition $transition): JsonResponse
    {
        $data = $request->validated();
        $transition->update($data);
        return response()->json($transition);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transition $transition): JsonResponse
    {
        $transition->delete();
        return response()->json(['message' => 'Transition deleted']);
    }
}
