<?php

return [

    /*
     * The fully qualified class name of the default transition.
     */
    'default_transition' => Spatie\ModelStates\DefaultTransition::class,
    'default_route' => 'App\\States\\',
    'namespace' => 'App\\States',
    'default_dir' => app_path('States'),
];
