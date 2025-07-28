<?php

namespace App\Listeners;

use App\Events\ExpenseApproved;
use App\States\Payment\VerifiedBySupervisor;

class ApproveExpenseListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExpenseApproved $event): void
    {
        $rule = $event->performedBy->getRoleNames()->first();
        $event->expense->state->transitionTo(VerifiedBySupervisor::class);
        $event->expense->update([
            'rejection_comment' => null,
        ]);

    }
}
