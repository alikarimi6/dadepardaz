<?php

namespace App\Models;

use App\States\Payment\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\ModelStates\HasStates;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles , HasApiTokens , HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public $guard_name = 'sanctum';
    protected $fillable = [
        'name',
        'email',
        'phone' ,
        'national_code'
    ];
    protected $table = 'users';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function expenses () : HasMany
    {
        return $this->hasMany(Expense::class);
    }
    public function ibans () : HasMany
    {
        return $this->hasMany(UserIban::class);
    }

    public function paymentStatuses(): HasMany
    {
        return $this->hasMany(PaymentStatus::class);
    }
}
