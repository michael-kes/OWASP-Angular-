<?php

/**
 * Response class
 */

class Response{

    # Json response opstellen: Content-Type wordt ingesteld en de response status
    public static function json(array $data, $status = 200)
    {
        header('Content-Type: application/json');
        $body = json_encode($data, JSON_PRETTY_PRINT);

        return static::send($body, $status);
    }

    # Set the response status
    public static function send($body, $status = 200)
    {
        http_response_code($status);
        return $body;
    }
}