<?php

namespace App\Services\Expense;
use App\Events\ExpenseApproved;
use App\Events\ExpenseRejected;
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

    public static function attemptTransition(Expense $expense, $data): bool
    {

        $roleName = auth('sanctum')->user()->getRoleNames()->first();
        $currentStateModel = $expense->state;
        if (!$currentStateModel) {
            throw new \Exception("Current state not found.");
        }
        $nextStateName = self::getNextState($roleName, $data['action'])->name;

        if (!$nextStateName) {
            throw new \Exception("Target state not found.");
        }
        if ($nextStateName == 'approved') {
            if (!isset($data['payment_method'])){
                throw new \Exception("payment_method is required.");
            }
        }

        if ($nextStateName == 'rejected') {
            if (!isset($data['rejection_comment'])){
                throw new \Exception("rejection_comment is required.");
            }
        }

        $currentStateModel->transitionTo(
            $nextStateName
        );

        if ($nextStateName == 'approved') {
            event(new ExpenseApproved($expense, $data['payment_method']));
        }
        if ($nextStateName == 'rejected') {
            event(new ExpenseRejected($expense, $data['rejection_comment']));
        }

        PaymentStatusLogger::log(
            $expense->id,
            $nextStateName,
            $currentStateModel::$name,
            $roleName ,
            $data['comment'] ?? null

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
