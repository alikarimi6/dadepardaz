<?php

namespace App\Listeners;

use App\Events\ExpenseApproved;
use App\Events\ExpenseRejected;
use App\Notifications\ExpenseStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUserListener
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
    public function handle(ExpenseApproved | ExpenseRejected $event)
    {
        logger("email queued , user: $event->expense->user");
        $event->expense->user->notify(new ExpenseStatusNotification($event->status , $event->expense));
    }
}
