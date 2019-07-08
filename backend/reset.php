<?php

# SQLite database resetten en opnieuw invullen
DB::sqliteRemove();
require App::path().'/helpers/createWorkshopDatabase.php';

# Response data omzetten naar json en printen op het scherm
echo Response::json(['message' => 'database recreated']);