<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
