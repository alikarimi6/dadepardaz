<?php

namespace App\Models;

use App\States\Payment\PaymentStatus;
use App\States\StateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Spatie\ModelStates\HasStates;

class Expense extends Model
{
    use HasStates , HasFactory;
    protected $fillable = [
        'category_id' ,
        'user_id' ,
        'amount' ,
        'status' ,
        'rejection_comment' ,
        'iban' ,
        'paid_at' ,
        'state' ,
    ];
    protected $states = [
        'state' => StateStatus::class,
    ];
    protected $casts = [
        'state' => StateStatus::class,
    ];
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function attachment() : HasOne
    {
        return $this->hasOne(ExpenseAttachment::class);
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(ExpensePaymentLog::class);
    }

    public function expenseStatusLog(): HasMany
    {
        return $this->hasMany(ExpenseStatusTransition::class);
    }

    public static function registerStates(): array
    {
        return [
            'state' => StateStatus::config(),
        ];
    }
}
