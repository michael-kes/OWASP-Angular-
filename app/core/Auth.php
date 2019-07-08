<?php

/**
 * Autorisatie class
 */

class Auth
{
    # Cookie settings
    protected static $cookieName = __CLASS__;
    protected static $cookieDuration = 0;

    # Remember the authorized user
    protected static $user;

    # Load configuration settings when this class is autoloaded
    public static function _autoload()
    {
        static::$cookieName = Config::get('user.cookie.name', static::$cookieName);
        static::$cookieDuration = Config::get('user.cookie.duration', static::$cookieDuration);
    }

    # Get the authorized user
    public static function user($autoCreate = false)
    {
        if (static::$user == null)
        {
            # Try to get the token from the cookie
            if ($token = Cookie::get(static::$cookieName))
            {
                # Get user by token or create
                static::$user = User::getByToken($token) ?: User::create();
            }
            elseif ($autoCreate)
            {
                # Create a new user
                static::$user = User::create();
            }
            else
            {
                throw new Exception('Not authorized');
            }

            # Store the auth cookie token
            Cookie::set(static::$cookieName, static::$user->token, static::$cookieDuration);
        }

        return static::$user;
    }
}