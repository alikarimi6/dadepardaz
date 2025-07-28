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
        $event->expense->update([
            'status' => 'approved',
            'rejection_comment' => null,
        ]);
    }
}
