<?php

namespace App\States\Payment;

use Illuminate\Database\Eloquent\Model;

class Requested extends PaymentStatus
{
    public static $name = 'requested';

    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        // TODO: Implement get() method.
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        // TODO: Implement set() method.
    }
}
