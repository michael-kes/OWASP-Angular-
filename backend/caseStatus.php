<?php

# Deze Request is gemaakt om een status van een case aan te passen.

# Connect to the mysql database
DB::mysqlConnection($conn);

# Data die is gepost naar de server ophalen
$input = Request::input(true);

$status = Arr::get($input, 'status');
$id = Arr::get($input, 'id');

$result = $mysql->query('SELECT count(*) FROM user_case WHERE case_id = '.$id.' AND user_token = "'.$user->token.'"');

if ($result->fetchColumn() == 0)
    $mysql->query('INSERT INTO user_case (case_id, user_token, status) VALUES ('.$id.', "'.$user->token.'", "none")');


$stmt = $conn->prepare('UPDATE user_case SET status = :status WHERE case_id = :id AND user_token = :token');
$stmt->bindValue(':status', $status, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':token', $user->token, PDO::PARAM_STR);
$stmt->execute();

# Response data omzetten naar json en printen op het scherm
echo Response::json(['status' => 'success']);