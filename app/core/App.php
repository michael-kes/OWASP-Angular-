<?php

/**
 * Applicatie class
 */

class App {

    protected static $basePath;

    public static function init($basePath)
    {
        static::$basePath = $basePath;

        $storagePath = static::storagePath();

        # Give write access to the storagePath
        if ( ! file_exists($storagePath))
        {
            mkdir($storagePath, 0755);
            mkdir($storagePath.'/db', 0755);
        }
    }

    public static function response()
    {
        $url = Request::url(true);
        $method = Request::method();
        $routes = static::routes();

        if (isset($routes[$url][$method]))
        {
            if (file_exists($filepath = App::basePath().'/backend/'.$routes[$url][$method]))
            {
                return $filepath;
            }
        }

        return false;
    }

    public static function routes()
    {
        $routes = require static::path().'/routes.php';

        $arr = [];
        foreach ($routes as $url => $filename)
        {
            $parts = explode(' ', $url, 2);

            if (count($parts) == 1)
            {
                Arr::set($arr, '/app/'.trim($url, '/').'.GET', $parts[0]);
                continue;
            }

            list($methods, $url) = $parts;

            foreach (explode('|', $methods) as $method)
            {
                Arr::set($arr, '/app/'.trim($url, '/').'.'.$method, $filename);
            }
        }

        return $arr;
    }

    /**
     * Get the base path of the installation.
     *
     * @return string
     */
    public static function basePath()
    {
        return static::$basePath;
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @return string
     */
    public static function path()
    {
        return static::$basePath.DIRECTORY_SEPARATOR.'app';
    }

    /**
     * Get the path to the application configuration files.
     *
     * @return string
     */
    public static function configPath()
    {
        return static::$basePath.DIRECTORY_SEPARATOR.'config';
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public static function publicPath()
    {
        return static::$basePath.DIRECTORY_SEPARATOR.'public';
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public static function storagePath()
    {
        return static::path().DIRECTORY_SEPARATOR.'storage';
    }
}