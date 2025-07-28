<?php

namespace App\Console\Commands\Pay;

use App\Jobs\ScheduledBankPayment;
use App\Models\ExpensePaymentLog;
use Illuminate\Console\Command;

class ProcessSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'process schedule';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payments = ExpensePaymentLog::where('method', 'scheduled')
            ->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($payments as $payment) {
            try {
                $bank = $payment->bank_id;
                $iban = $payment->expense_id;
                $amount = $payment->expense_id;

                ScheduledBankPayment::dispatch($bank, $iban, $amount, $payment->expense_id);

                $payment->update(['status' => 'paid']);

            } catch (\Throwable $e) {
                $payment->update([
                    'status' => 'failed',
                    'exception_type' => get_class($e),
                ]);
            }
        }
    }
}
