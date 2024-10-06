<?php

require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Database.php';
// include all PHP files under /app
foreach (glob(__DIR__ . '/../app/{*,*/*}.php', GLOB_BRACE) as $filename) {
    require_once $filename;
}

use App\Controllers\CarController;

$router = new Router();

$router->get('ping', function () {
    return "It's working!!";
});

$router->get('cars', function () {
    $carsController = new CarController();
    return $carsController->get_all_cars();
});

$router->dispatch($_SERVER['REQUEST_URI']);
