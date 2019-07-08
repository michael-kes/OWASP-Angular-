<?php

/**
 * Applicatie instellingen
 */

return [

    # Debug mode print error messages op het scherm.
    'debug' => true,

    'user' => [

        'cookie' => [
            'name'      => 'auth_token',
            'duration'  => Cookie::WEEK,
        ],
    ],

    # Vul hier de instellingen van jouw database in
    'database' => [

        'host'      => '93.191.133.193',
        'name'      => 'owasp_owasp',
        'username'  => 'owasp_owasp',
        'password'  => 'QuDh4PDS3b',
        'charset'   => 'utf8'
    ]

];