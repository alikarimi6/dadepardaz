<?php

namespace App\Models;


use App\States\Payment\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\ModelStates\HasStates;

class ExpensePaymentLog extends Model
{
    use HasStates;
    protected $table = 'payment_logs';
    protected $fillable = [
        'bank_id',
        'expense_id',
        'status',
        'exception_type',
        'method' ,
        'scheduled_at'
    ];

    protected $states = [
        'state' => PaymentStatus::class,
    ];
    protected $casts = [
        'state' => PaymentStatus::class,
    ];
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function paymentStatusTransition(): HasOne
    {
        return $this->hasOne(PaymentStatusTransition::class , 'payment_id');
    }
}
