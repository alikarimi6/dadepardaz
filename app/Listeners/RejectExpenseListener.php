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
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle( $event): void
    {
        $event->expense->update([
            'rejection_comment' => $event->rejection_comment,
        ]);
    }
}
