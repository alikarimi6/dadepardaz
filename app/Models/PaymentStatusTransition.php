<?php

namespace App\Models;

use App\States\Payment\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\ModelStates\HasStates;
use Spatie\Permission\Models\Role;

class PaymentStatusTransition extends Model
{
    protected $table = 'payment_status_transitions';
    protected $fillable = [
        'payment_log_id' ,
        'from_status' ,
        'to_status' ,
        'user_id',
        'role' ,
        'transitioned_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentLog(): BelongsTo
    {
        return $this->belongsTo(ExpensePaymentLog::class , 'payment_log_id');
    }

    public function expense()
    {
//        return $this->belongsToThrough(Expense::class);
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class , 'role' , 'name');
    }
}
