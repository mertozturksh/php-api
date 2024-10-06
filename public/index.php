<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../core/autoload.php';

use App\Controllers\CarController;
use Core\Router;

$router = new Router();

$router->get('/', function () {
    return "It's working!!";
});

$router->get('cars', function () {
    $carsController = new CarController();
    return $carsController->get_all_cars();
});

$router->dispatch($_SERVER['REQUEST_URI']);
