<?php

# Connected to sqlite database
DB::sqliteConnection($conn);

$query = '
CREATE TABLE IF NOT EXISTS Persons
(
    PersonID INTEGER PRIMARY KEY AUTOINCREMENT,
    name,
    email,
    password
)
';
$conn->query($query);

$query = 'INSERT INTO persons VALUES (NULL, "Heinrich", "heinrich@gmail.com", "topsecret")';
$conn->query($query);


$query = '
CREATE TABLE IF NOT EXISTS Message
(
    MessageID INTEGER PRIMARY KEY AUTOINCREMENT,
    author,
    message,
    insertDate
)
';
$conn->query($query);

$query = 'INSERT INTO Message ("author", "message", "insertDate") VALUES ("Harrie", "berichtje", "'.date("d-m-Y H:i").'")';
$conn->query($query);