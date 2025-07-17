<?php

namespace App\Jobs;

use App\Services\Bank\BankInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class ScheduledBankPayment implements ShouldQueue
{
    use Queueable , Dispatchable;
    protected BankInterface $bank;
    protected string $iban;
    protected string $amount;
    protected string $expenseId;
    /**
     * Create a new job instance.
     */
    public function __construct(BankInterface $bank, string $iban, string $amount , $expenseId)
    {
        $this->bank = $bank;
        $this->iban = $iban;
        $this->amount = $amount;
        $this->expenseId = $expenseId ;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->bank->pay($this->amount, 'schedule callback' , $this->expenseId);
    }
}
