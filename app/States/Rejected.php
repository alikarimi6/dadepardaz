<?php

    namespace App\States;
    use App\States\StateStatus;
    use Spatie\ModelStates\State;

    class Rejected extends StateStatus
    {
        public static  $name = "rejected";
    }