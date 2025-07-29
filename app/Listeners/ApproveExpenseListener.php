<?php

namespace App\Listeners;

use App\Events\ExpenseApproved;
use App\Services\Expense\ExpenseStateService;
use App\States\Payment\Paid;
use App\States\Payment\Requested;
use App\States\Payment\VerifiedByOwner;
use App\States\Payment\VerifiedBySupervisor;

class ApproveExpenseListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private ExpenseStateService $expenseStateService
    )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExpenseApproved $event): void
    {/*
        $currentState = get_class($event->expense->state);
        $role = $event->performedBy->getRoleNames()->first();
        $transitions = [
            'supervisor' => [
                Requested::class => VerifiedBySupervisor::class,
            ],
            'owner' => [
                VerifiedBySupervisor::class => VerifiedByOwner::class,
                VerifiedByOwner::class => Paid::class
            ]];
        if (isset($transitions[$role][$currentState])) {
            $nextState = $transitions[$role][$currentState];
            $event->expense->state->transitionTo($nextState);
            $event->expense->update([
                'rejection_comment' => null,
            ]);
        }*/

        $this->expenseStateService->transition(
            expense: $event->expense,
            performedBy: $event->performedBy,
            action: $event->status
        );
    }
}
