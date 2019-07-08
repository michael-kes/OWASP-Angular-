<?php

# SQLite database resetten en opnieuw invullen
DB::sqliteRemove();
require App::path().'/helpers/createWorkshopDatabase.php';

# Mysql query uitvoeren
DB::mysqlConnection($mysql);
$mysql->exec('DELETE FROM user_case WHERE user_token = "'.$user->token.'"');

# Response data omzetten naar json en printen op het scherm
echo Response::json(['message' => 'progression restored']);