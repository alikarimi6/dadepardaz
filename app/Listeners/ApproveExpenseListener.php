<?php

namespace App\Listeners;

use App\Events\ExpenseApproved;
use App\Events\ExpenseRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
