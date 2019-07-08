<?php

/**
 * Class om Classes automatisch te laden
 */

class Classloader
{
    /**
     * Register the Classloader to the SPL autoload stack
     *
     * @return  void
     */
    public static function register()
    {
        spl_autoload_register('static::load', true, true);
    }

    /**
     * Unregister the Classloader from the SPL autoloader stack
     *
     * @return  void
     */
    public static function unregister()
    {
        spl_autoload_unregister('static::load');
    }

    /**
     * Laad een class vanuit de 'app/core' folder
     */
    public static function load($class)
    {
        $path = __DIR__.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';

        # Does the class file exists
        if (file_exists($path))
        {
            # Include the file containing the class
            require $path;

            # Check if the class has been found within the given file path
            if (class_exists($class) or interface_exists($class))
            {
                # Initialize the class by calling the static _autoload() method
                if (method_exists($class, '_autoload') and is_callable($class.'::_autoload'))
                {
                    call_user_func($class.'::_autoload');
                }

                return true;
            }
        }

        return false;
    }
}