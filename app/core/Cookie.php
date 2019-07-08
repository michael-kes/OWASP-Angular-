<?php

/**
 * Cookie helper class om het leven makkelijker te maken
 */

class Cookie
{

    const HOUR      = 3600;         # 60 min
    const DAY       = 86400;        # 24 hour
    const WEEK      = 604800;       # 7  days
    const MONTH     = 2592000;      # 30 days
    const YEAR      = 31536000;     # 365 days

    # Set een cookie
    public static function set($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        # Add the current time so we have an offset if not 0
        $expire = (int) $expire and $expire += time();

        # Set the cookie
        if ($success = setcookie($name, $value, $expire, $path ?: '/', $domain, (bool) $secure, (bool) $httpOnly))
        {
            Arr::set($_COOKIE, $name, $value);
        }

        return $success;
    }

    # Cookie ophalen
    public static function get($name = null, $default = null)
    {
        return Arr::get($_COOKIE, $name, $default);
    }

    # Kijken of een cookie bestaad
    public static function has($name)
    {
        return Arr::has($_COOKIE, $name);
    }

    # Cookie verwijderen
    public static function remove($name, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        $success = true;

        if ($success = setcookie($name, null, time()-self::HOUR, $path ?: '/', $domain, (bool) $secure, (bool) $httpOnly))
        {
            Arr::remove($_COOKIE, $name);
        }

        return $success;
    }
}