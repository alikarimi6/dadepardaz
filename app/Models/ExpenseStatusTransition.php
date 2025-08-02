<?php

namespace App\Models;

use App\States\Payment\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\ModelStates\HasStates;
use Spatie\Permission\Models\Role;

class ExpenseStatusTransition extends Model
{
    protected $table = 'expense_status_transitions';
    protected $fillable = [
        'expense_id' ,
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
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class , 'role' , 'name');
    }
}
