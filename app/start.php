<?php

/**
 * Dit bestand wordt altijd geladen en zorgt dat de applicatie wordt opgestart
 */

# Foutmelding instellen
error_reporting(-1);
ini_set('display_errors', 1);

# Classloader inladen
require __DIR__.'/core/Classloader.php';

# Help functies inladen
require __DIR__.'/helpers/functions.php';

# Classloader registreren
Classloader::register();

# Applicatie opstarten
App::init(realpath(__DIR__.'/../'));

# Maak de MYSQL Database tabellen aan (deze functie kan later wel weg)
require __DIR__.'/helpers/createDatabase.php';

# SQLite database aanmaken als deze nog niet bestaad
if ( ! DB::sqliteExists())
    require __DIR__.'/helpers/createWorkshopDatabase.php';

# Tijdzone instellen
date_default_timezone_set(Config::get('timezone', date_default_timezone_get()));

/**
 * -------------------------------------------------------------------
 *  Shutdown, error and exception handlers
 * -------------------------------------------------------------------
 */

register_shutdown_function(function()
{
    # Sluit alle database connecties
    DB::closeAll();
    return;

});

set_exception_handler(function(Exception $e)
{
    # Content-Type weer resetten
    header('Content-Type: text/html');

    # Als de debug mode op false staat zijn we direct klaar
    if ( ! Config::get('debug', false))
    {
        Response::send('500 Internal Server Error', 500);
        exit;
    }

    # Class en Namespace
    $class = get_class($e);
    $namespace = '';

    # Class en Namespace splitsen
    if ($pos = strrpos($class, '\\'))
    {
        $namespace = '<div style="font-weight:normal;">Namespace: '.substr($class, 0, $pos).'</div>';
        $class = substr($class, $pos+1);
    }

    # create error message
    $header = 'Uncaught <i><u>'.$class.'</u></i><br><i>'.$namespace.'</i> Exception';

    $function = $e->getTrace();
    $function = isset($function[0]) ? $function[0]['function'].'()' : null;

    $message = $e->getMessage().'<br><b>Function: '.$function.'</b>';
    $filepath = $e->getFile();
    $line = $e->getLine();

    require App::path().'/helpers/error.php';
    Response::send(null, 500);
    exit;
});


set_error_handler(function($severity, $message, $filepath, $line)
{
    # Controleer op onderdrukte errors @
    if (error_reporting() === 0) return;

    # Content-Type weer resetten
    header('Content-Type: text/html');

    # Als de debug mode op false staat zijn we direct klaar
    if ( ! Config::get('debug', false))
    {
        Response::send('500 Internal Server Error', 500);
        exit;
    }

    $header = 'Notice!';

    require App::path().'/helpers/error.php';
    Response::send(null, 500);
    exit;
});