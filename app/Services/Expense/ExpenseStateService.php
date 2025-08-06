<?php

namespace App\Services\Expense;
use App\Events\ExpenseApproved;
use App\Events\ExpenseRejected;
use App\Models\Expense;
use App\Models\ExpenseStatusTransition;
use App\Models\State;
use App\Models\Transition;
use App\States\Payment\PaymentStatus;
use App\States\PendingPay;
use App\States\Rejected;
use App\States\StateStatus;
use Dflydev\DotAccessData\Data;

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

        $currentStateClass = $expense->state;
        if (!$currentStateClass) {
            throw new \Exception("Current state not found.");
        }
        $nextStateModel = self::getNextState($roleName, $data['action']);
        $nextStateClass = StateStatus::mapActionToStateClass($nextStateModel->action);

        if (!$nextStateModel) {
            throw new \Exception("Target state not found.");
        }
        $fromStatus = $expense->expenseStatusLog->last()->to_status ?? 'requested';
        $fromStatusModel = State::query()->where(['name' => $fromStatus])->first();

        $exists = Transition::query()->where('from_state_id', $fromStatusModel->id)
            ->where('to_state_id', $nextStateModel->id)
            ->exists();

        if (!$exists) {
            throw new \Exception("no transition available.");
        }

        $currentStateClass->transitionTo(
            $nextStateClass);


//        dd($currentStateModel->transitionableStates());


        if ($nextStateClass == PendingPay::class) {
            event(new ExpenseApproved($expense, $data['payment_method']));
        }
        if ($nextStateClass == Rejected::class) {
            event(new ExpenseRejected($expense, $data['rejection_comment']));
        }


        PaymentStatusLogger::log(
            $expense->id,
            $nextStateModel->name,
            $fromStatus ?? 'requested',
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
