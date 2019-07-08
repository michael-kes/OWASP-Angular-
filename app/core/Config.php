<?php

/**
 * Deze class regelt het uitlezen van de config.php file
 */

class Config {

    protected static $container = [];

    public static function _autoload()
    {
        $file = App::path().'/config.php';

        if (file_exists($file))
        {
            static::$container = require $file;
        }
        else throw new Exception('Configfile not found!');
    }

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public static function add($key, $value)
    {
        if ( ! static::has($key))
        {
            static::set($key, $value);
        }
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function get($key = null, $default = null)
    {
        $array = static::$container;

        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment)
        {
            if ( ! is_array($array) || ! array_key_exists($segment, $array))
            {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @return bool
     */
    public static function has($key)
    {
        $array = static::$container;

        if (empty($array) || is_null($key)) return false;

        if (array_key_exists($key, $array)) return true;

        foreach (explode('.', $key) as $segment)
        {
            if ( ! is_array($array) || ! array_key_exists($segment, $array))
            {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public static function set($key, $value)
    {
        $array =& static::$container;

        if (is_null($key)) return;

        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if ( ! isset($array[$key]) || ! is_array($array[$key]))
            {
                $array[$key] = [];
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
    }
}