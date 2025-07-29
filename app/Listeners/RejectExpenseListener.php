<?php

namespace App\Listeners;

use App\Events\ExpenseRejected;
use App\Services\Expense\ExpenseStateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RejectExpenseListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private ExpenseStateService $expenseStateService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExpenseRejected $event): void
    {
        $this->expenseStateService->transition(
            expense: $event->expense,
            performedBy: $event->performedBy,
            action: $event->status ,
            comment: $event->rejection_comment
        );
    }
}
