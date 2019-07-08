<?php

/**
 * Request class
 */

class Request {

    # Opslag van bepaalde zaken zodat ze maar 1 keer hoeven worden opgesteld
    protected static $host;
    protected static $path;
    protected static $url;

    # _autoload wordt aangeroepen als deze class wordt geladen
    public static function _autoload()
    {
        # Scriptname ophalen
        $scriptName = Arr::get($_SERVER, 'SCRIPT_NAME', '/');

        # Host opstellen
        static::$host = static::scheme().'://'.Arr::get($_SERVER, 'HTTP_HOST');

        # Path vaststellen
        static::$path = substr_count($scriptName, '/') > 2 ? dirname($scriptName) : '';
    }

    # Method die check of dit een ajax request is
    public static function ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    # Methode om input uit te lezen (Als JSON naar de server is verstuurd)
    public static function input($array = false)
    {
        $input = json_decode(file_get_contents('php://input'));

        return $array ? (array) $input : (object) $input;
    }

    # Request method ophalen
    public static function method()
    {
        return Arr::get($_SERVER, 'REQUEST_METHOD', 'GET');
    }

    # Controle of de request een bepaalde methode bevat.
    public static function isMethod($method)
    {
        return static::method() == strtoupper($method);
    }

    # Host ophalen
    public static function host()
    {
        return static::$host;
    }

    # Base (host/path)
    public static function base()
    {
        return static::$host.static::$path;
    }

    # path ophalen
    public static function basepath()
    {
        return static::$path;
    }

    # Scheme van de Request ophalen
    public static function scheme()
    {
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
    }

    # Request url bepalen (vanaf de basepath)
    public static function url($trim = false)
    {
        if (static::$url === null)
        {
            # Get the request uri
            $uri = Arr::get($_SERVER, 'REDIRECT_URL', Arr::get($_SERVER, 'REQUEST_URI', ''));

            # Count the slashes in the basepath
            $slashes = substr_count(static::$path, '/');

            # Check if there is a url and remove the base path from the uri
            if (substr_count($uri, '/') > $slashes and $uri = strstr_nth($uri, '/', $slashes+1))
            {
                # Decode the uri
                $uri = rawurldecode($uri);

                # Remove the query string
                $uri = preg_replace('/\?.*/', null, $uri);

                # Run security filters on the uri
                $clean = preg_replace(['/\.+\//', '/\/+/'], '/', $uri);
                $clean = htmlentities($clean, ENT_QUOTES, 'UTF-8');

                # If the uri is altered by the security filter
                if ($uri !== $clean)
                {
                    # Throw http not found exception when the uri has invalid charachters
                    throw new Exception('The request url contains invalid charachters');
                }
            }
            else
            {
                $uri = '';
            }

            static::$url = '/'.$uri;
        }

        return $trim ? rtrim(static::$url, '/') : static::$url;
    }
}