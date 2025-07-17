<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseAttachment extends Model
{
    protected $fillable = [
        'expense_id' ,
        'file_path' ,
    ];
    public function expense() : BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
