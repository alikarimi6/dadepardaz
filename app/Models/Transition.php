<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class Transition extends Model
{
    protected $fillable = ['name', 'from_state_id', 'to_state_id'];

    public function fromState(): BelongsTo
    {
        return $this->belongsTo(State::class, 'from_state_id');
    }

    public function toState(): BelongsTo
    {
        return $this->belongsTo(State::class, 'to_state_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'transition_roles');
    }
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

}
