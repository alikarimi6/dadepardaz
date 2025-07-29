<?php

namespace App\States\Payment;

use Illuminate\Database\Eloquent\Model;

class Requested extends PaymentStatus
{
    public static $name = 'requested';
}
