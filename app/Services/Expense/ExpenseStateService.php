<?php

namespace App\Services\Expense;
use App\Models\Expense;
class ExpenseStateService
{
    public static function checkPermission(Expense $expense , $status ): bool
    {
        $role = auth()->user()->getRoleNames()->first();
        $allowedByRole = config('state-rules')[$role];
        $allowedState = $allowedByRole[$status] ?? [];
        $nextStates = $expense->state->transitionableStates();
        if ( !in_array($allowedState, $nextStates))
            return false;
        $expense->state->transitionTo($allowedState);
        return true;
    }
}
