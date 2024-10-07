<?php

use App\Controllers\CarController;



$router->get('/', function () {
    return "It's working!!";
});

$router->get('cars', function () {
    $carsController = new CarController();
    return $carsController->get_all_cars();
});

$router->get('/user/:id', function($id) {
    return ['user_id' => $id];
});