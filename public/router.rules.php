<?php

use App\Controllers\CarController;
use App\Controllers\TestController;

// $router->middleware(['Core\Middlewares\RequestLogger', 'log']);

$router->get('/', function () {
    return "It's working!!";
}, [['callback' => ['Core\Middlewares\RequestLogger', 'logResponse'], 'before' => false]]);

$router->get('test', function () {
    $controller = new TestController();
    return $controller->test($_REQUEST);
});

$router->get('cars', function () {
    $carsController = new CarController();
    return $carsController->get_all_cars();
});

$router->get('cars/:id', function ($id) {
    $carsController = new CarController();
    return $carsController->get_car($id);
});
