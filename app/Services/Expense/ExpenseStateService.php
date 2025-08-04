<?php

namespace App\Services\Expense;
use App\Models\Expense;
use App\Models\State;
use App\Models\Transition;
class ExpenseStateService
{
    public static function checkPermission(Expense $expense , $status ): bool
    {
        $role = auth()->user()->getRoleNames()->first();
        $allowedByRole = config('state-rules')[$role];
        $allowedState = $allowedByRole[$status] ?? [];
        $currentState = $expense->state;
        $nextStates = $expense->state->transitionableStates();
        if ( !in_array($allowedState, $nextStates))
            return false;
        $expense->state->transitionTo($allowedState);
        PaymentStatusLogger::log($expense->id , $allowedState , $currentState , $role);
        return true;

    }
//        todo : log and verify if modir approved

    public static function attemptTransition(Expense $expense, string $action): bool
    {
        $roleName = auth('sanctum')->user()->getRoleNames()->first();
        $currentStateModel = State::query()->find( $expense->state->id);

        if (!$currentStateModel) {
            throw new \Exception("Current state not found.");
        }

        $nextStateModel = self::getNextState($roleName, $action);
        if (!$nextStateModel) {
            throw new \Exception("Target state not found.");
        }

        $transition = Transition::query()->where('from_state_id', $currentStateModel->id)
            ->where('to_state_id', $nextStateModel->id)
            ->first();

        if (!$transition) {
            throw new \Exception("Transition not allowed from '$currentStateModel->name' to '$action'");
        }


        $stateClass = $expense->state->class;

        /** @var \Spatie\ModelStates\State $stateInstance */
        $stateInstance = new $stateClass($expense);
//      todo: use package to handle the transitions
        $expense->state_id = $nextStateModel->id;
        $expense->save();

        PaymentStatusLogger::log(
            $expense->id,
            $nextStateModel->name,
            $currentStateModel->name,
            $roleName
        );

        return true;
    }
    public static function getNextState(string $roleName , $action)
    {
        return State::query()->whereHas('transitionRoles.role', fn($q) => $q->where('name', $roleName))
            ->where('action', $action)
            ->first();
    }
}
