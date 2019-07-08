<?php

# Deze pagina behandeld alle front end requests

# Applicatie opstarten
require __DIR__.'/../app/start.php';

# Alleen GET Requests toestaan
if ( ! Request::isMethod('GET') || Request::ajax())
{
    # 404 Response teruggeven
    Response::send('', 404);
    exit;
}

# Gebuiker aanmaken als deze nog niet bestaad
Auth::user(true);

?><!DOCTYPE html>
<html lang="nl" data-ng-app="app">
<head>
    <meta charset="utf-8">
    <base href="<?php echo Request::base(); ?>/">

    <title data-ng-bind="pageTitle">Owasp</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/icons.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <!-- jQuery -->
    <script src="js/jquery/jquery.min.js"></script>
    <script src="js/jquery/jquery.md5.js"></script>

    <!-- AngularJS -->
    <script src="js/angular/angular.min.js"></script>
    <script src="js/angular/angular-sanitize.min.js"></script>
    <script src="js/angular/angular-animate.min.js"></script>
    <script src="js/angular/other/angular-ui-router.min.js"></script>
    <script src="js/angular/other/angular-local-storage.min.js"></script>

    <!-- Application -->
    <script src="js/app.js"></script>
    <script src="js/controllers.js"></script>
    <script src="js/services.js"></script>
    <script src="js/directives.js"></script>
</head>
<body>
    <div class="wrapper" data-ui-view></div>
</body>
</html>