<?php

    namespace App\States;
    use App\States\StateStatus;
    use Spatie\ModelStates\State;

    class Approved extends StateStatus
    {
        public static  $name = "approved";
    }