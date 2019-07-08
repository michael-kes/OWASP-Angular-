<?php

/**
 * Database class, Maakt gebruik van PDO
 */

class DB {

    # Bijhouden welke connecties er zijn
    protected static $storage = [];

    # Options voor PDO
    protected static $options = [
        PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_OBJ
    ];

    # Mysql connection aanroepen (instellingen staan in de config file)
    # De meegegeven variable bevat de connectie na het aanroepen van deze methode
    public static function mysqlConnection(&$conn)
    {
        # Connectie proberen op te halen uit de storage
        $conn = Arr::get(static::$storage, 'mysql');

        # Bestaad deze connectie nog niet?
        if ( ! $conn instanceof PDO)
        {
            # data source name
            $dsn = 'dbname='.Config::get('database.name').';host='.Config::get('database.host', 'localhost').';charset='.Config::get('database.charset', 'utf8');

            $user = Config::get('database.username');
            $pass = Config::get('database.password');

            # Connectie maken
            $conn = new PDO('mysql:'.$dsn, $user, $pass, static::$options);

            # Connectie opslaan in de storage
            static::$storage['mysql'] =& $conn;
        }
    }

    # SQLite connection aanroepen
    # De meegegeven variable bevat de connectie na het aanroepen van deze methode
    public static function sqliteConnection(&$conn)
    {
        # Filepath opstellen
        $path = static::path(Auth::user(true)->token);

        # Connectie proberen op te halen uit de storage
        $conn = Arr::get(static::$storage, $path);

        # Bestaad deze connectie nog niet?
        if ( ! $conn instanceof PDO)
        {
            # Connectie maken
            $conn = new PDO('sqlite:'.$path, null, null, static::$options);

            # Connectie opslaan in de storage
            static::$storage[$path] =& $conn;
        }
    }

    # Methode om de sqlite filepath op te stellen
    protected static function path($name)
    {
        $name = strtolower($name);
        $name = pathinfo($name, PATHINFO_EXTENSION) == 'sqlite' ? $name : $name.'.sqlite';
        return App::storagePath().'/database/'.$name;
    }

    # Methode om te zien of een sqlitefile bestaad
    public static function sqliteExists()
    {
        return file_exists(static::path(Auth::user(true)->token));
    }

    # methode om een sqlite file te verwijderen
    public static function sqliteRemove()
    {
        # Filepath opstellen
        $path = static::path(Auth::user(true)->token);

        # Sluit de verbinding
        static::close($path);

        # Verwijder de sqlite database
        if (file_exists($path))
            return unlink($path);
    }

    # Sluit de mysql connectie
    public static function mysqlClose()
    {
        # Close the connection
        static::close('mysql');
    }

    # Sluit de sqlite connectie
    public static function sqliteClose()
    {
        # Close the connection
        static::close(static::path(Auth::user(true)->token));
    }

    # Sluit alle open verbindingen
    public static function closeAll()
    {
        foreach (array_keys(static::$storage) as $name)
        {
            static::close($name);
        }
    }

    # Private methode om een connectie te sluiten
    private static function close($name)
    {
        static::$storage[$name] = null;
        unset(static::$storage[$name]);
    }
}