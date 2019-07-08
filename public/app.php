<?php

# Deze pagina behandeld alle back end requests

# Applicatie opstarten
require __DIR__.'/../app/start.php';

# Autorisatie check
$user = Auth::user();

# Zoek het bijbehorend bestand voor deze request
if ($response = App::response())
{
    # Behandel de request
    require $response;
}
else
{
    # 404 - Not Found
    Response::send('', 404);
}