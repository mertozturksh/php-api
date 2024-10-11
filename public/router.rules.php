<?php

use App\Controllers\CarController;
use App\Controllers\TestController;
use App\Middlewares\RequestLoggerMiddleware;

$router->middleware([RequestLoggerMiddleware::class, 'handle']);

$router->get('/', function () {
    return "It's working!!";
}, //[
//    ['callback' => [RequestLoggerMiddleware::class, 'handle'], 'before' => true]
//]
);

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
