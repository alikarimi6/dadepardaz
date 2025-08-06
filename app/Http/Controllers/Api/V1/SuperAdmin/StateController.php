<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\State\StateStoreRequest;
use App\Http\Requests\State\StateUpdateRequest;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isBool;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(State::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StateStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->has('is_default')) {
            State::query()->where('is_default', true)->update(['is_default' => false]);
        }
        $state = State::query()->create($data);
        return response()->json($state, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state): JsonResponse
    {
        return response()->json($state);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StateUpdateRequest $request, State $state): JsonResponse
    {
        $data = $request->validated();
        if ($request->has('is_default') &&  $data['is_default']) {
            if ($request->boolean('is_default')) {
                $state->update(['is_default' => true]);
            } else {
                $state->update(['is_default' => false]);
            }
        }

        $state->update($data);
        return response()->json($state);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state): JsonResponse
    {
        $state->delete();
        return response()->json(['message' => 'State deleted']);
    }
}
