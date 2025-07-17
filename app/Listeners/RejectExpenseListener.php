<?php

namespace App\Listeners;

use App\Events\ExpenseRejected;
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
    public function handle(ExpenseRejected $event): void
    {
        $event->expense->update([
            'status' => 'rejected',
            'rejection_comment' => $event->rejection_comment,
        ]);
    }
}
