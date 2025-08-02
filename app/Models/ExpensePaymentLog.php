<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpensePaymentLog extends Model
{
    protected $table = 'payment_logs';
    protected $fillable = [
        'bank_id',
        'expense_id',
        'status',
        'exception_type',
        'method' ,
        'scheduled_at'
    ];
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
