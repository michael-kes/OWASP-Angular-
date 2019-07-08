<?php

/**
 * Hier kunnen back-end urls worden geregistreerd
 * Back-end urls beginnen met {host}/app{URL}
 *
 * Als key moet eerste de request method worden aangeven
 * dit er 1 zijn of meerdere zoals 'GET|POST|PUT|DELETE'
 * Vervolgens volgt een spatie en de bijbehorende URL
 */

return [

    # Get information about all the cases
    'GET /cases' => 'cases.php',
    'POST /case/status' => 'caseStatus.php',

    # Recreate the database
    'GET /reset' => 'reset.php',
    'GET /restart' => 'restart.php',

    # API routes for the cases
    'POST|GET /api' => 'api.php',
    'POST|GET /case1' => 'case1.php',
    'POST|GET /case2' => 'case2.php',
    'POST|GET /case3' => 'case3.php',
    'POST|GET /case4' => 'case4.php',
    'POST|GET /case5' => 'case5.php',
    'POST|GET /case6' => 'case6.php',
    'POST|GET /case7' => 'case7.php'

];