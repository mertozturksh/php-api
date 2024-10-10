<?php

use App\Controllers\CarController;
use App\Controllers\TestController;

$router->get('/', function () {
    return "It's working!!";
});

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
}, [['callback' => 'authMiddleware', 'before' => true], ['callback' => 'logResponseMiddleware', 'before' => false]]);
