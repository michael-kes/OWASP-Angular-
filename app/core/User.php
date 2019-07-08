<?php

/**
 * User class
 */

class User {

    public $token;
    public $name;

    # new User()
    public function __construct($token)
    {
        $this->token = $token;
        $this->name = 'name';
    }

    # Statische functie om een User te vinden adhv een token
    # Returns: null || User
    public static function getByToken($token)
    {
        DB::mysqlConnection($conn);

        $stmt = $conn->prepare('SELECT count(*) AS count FROM user WHERE token = :token');
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        # Nieuw user object teruggeven
        if ($stmt->fetch()->count == 1)
        {
            return new user($token);
        }
    }

    # Nieuwe gebruiker aanmaken
    # Returns: User
    public static function create()
    {
        # Verbinden met database
        DB::mysqlConnection($conn);

        # Gebruiker met unieke token aanmaken
        $success = false;
        while ( ! $success)
        {
            # Token aanmaken
            $token = static::generateToken();

            # Gebruiker aanmaken
            try { $success = $conn->exec('INSERT INTO user (token) VALUES (\''.$token.'\')'); }
            catch (Exception $e) {}
        }

        # Nieuw user object teruggeven
        return new user($token);
    }

    # Helper functie om een token te genereren
    public static function generateToken()
    {
        return md5(uniqid(mt_rand(), true));
    }

}