<?php

namespace App\Services\Expense;
use App\Models\Expense;
use App\Models\State;
use App\Models\Transition;
use App\States\VerifiedByEdari;
use App\States\VerifiedByMali;
use App\States\VerifiedByModir;

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
//        todo : fire events if modirapproved/full-reject

    public static function attemptTransition(Expense $expense, string $action , $comment = null): bool
    {

        $roleName = auth('sanctum')->user()->getRoleNames()->first();
        $currentStateModel = $expense->state;
        if (!$currentStateModel) {
            throw new \Exception("Current state not found.");
        }
        $nextStateName = self::getNextState($roleName, $action)->name;

        if (!$nextStateName) {
            throw new \Exception("Target state not found.");
        }

        $currentStateModel->transitionTo(
            $nextStateName
        );

        PaymentStatusLogger::log(
            $expense->id,
            $nextStateName,
            $currentStateModel::$name,
            $roleName ,
            $comment

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
