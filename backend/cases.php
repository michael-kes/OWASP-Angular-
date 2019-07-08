<?php

# Deze Request is gemaakt om alle cases en bijbehorende user data op te halen

DB::mysqlConnection($conn);

$data = $conn->query('
SELECT cases.*, user_case.status AS status, user_case.score AS score FROM cases
LEFT JOIN user_case ON cases.id = user_case.case_id AND user_case.user_token = "'.$user->token.'"
ORDER BY cases.id
')->fetchAll();

# Response data omzetten naar json en printen op het scherm
echo Response::json($data);