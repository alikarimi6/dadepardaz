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
    )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExpenseApproved $event): void
    {
        $event->expense->update([
            'rejection_comment' => null,
        ]);
    }
}
