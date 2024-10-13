<?php

use App\Middlewares\RequestLoggerMiddleware;

$router->middleware([RequestLoggerMiddleware::class, 'handle']);

$router->get(
    '/',
    function () {
        return "It's working!!";
    }, //[
    //    ['callback' => [RequestLoggerMiddleware::class, 'handle'], 'before' => true]
    //]
);

$router->get('test', function () use (&$sdk) {
    echo 'slm';
    return $sdk->controller('Example')->test($_REQUEST);
});

$router->get('cars', function () use (&$sdk) {
    return $sdk->controller('Example')->get_all_cars();
});

$router->get('cars/:id', function ($id) use (&$sdk) {
    return $sdk->controller('Example')->get_car($id);
});
