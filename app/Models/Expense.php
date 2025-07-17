<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id' ,
        'user_id' ,
        'amount' ,
        'status' ,
        'rejection_comment' ,
        'iban' ,
        'paid_at' ,
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
}
