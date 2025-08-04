<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Permission\Models\Role;

class State extends Model
{
    protected $fillable = ['name', 'is_default' , 'class'];

    public function fromTransitions(): HasMany
    {
        return $this->hasMany(Transition::class, 'from_state_id');
    }

    public function toTransitions(): HasMany
    {
        return $this->hasMany(Transition::class, 'to_state_id');
    }
    public function transitionRoles(): HasManyThrough
    {
        return $this->hasManyThrough(
            TransitionRole::class,
            Transition::class,
            'to_state_id',
            'transition_id',
            'id',
            'id'
        );
    }
}
