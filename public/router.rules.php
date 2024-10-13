<?php

use App\Middlewares\RequestLoggerMiddleware;

$router->middleware([RequestLoggerMiddleware::class, 'handle']);

$router->get(
    '/',
    function () {
        return ['status' => 200, 'data' => "It's working!"];
    }, //[
    //    ['callback' => [RequestLoggerMiddleware::class, 'handle'], 'before' => true],
    //]
);

$router->get('cars', function () use (&$sdk) {
    $result = $sdk->controller('Example')->getAll();
    if ($sdk->isError($result)) {
        return $result;
    }
    return ['status' => 200, 'data' => $result];
});

$router->get('cars/:id', function ($id) use (&$sdk) {
    $result = $sdk->controller('Example')->get($id);
    if ($sdk->isError($result)) {
        return $result;
    }
    return ['status' => 200, 'data' => $result];
});

$router->post('cars', function () use (&$sdk) {
    $result = $sdk->controller('Example')->create($_REQUEST);
    if ($sdk->isError($result)) {
        return $result;
    }
    return ['status' => 201, 'data' => 'Created successfully.'];
});

$router->put('cars/:id', function ($id) use (&$sdk) {
    $result = $sdk->controller('Example')->update($id, $_REQUEST);
    if ($sdk->isError($result)) {
        return $result;
    }
    return ['status' => 204];
});

$router->delete('cars/:id', function ($id) use (&$sdk) {
    $result = $sdk->controller('Example')->delete($id);
    if ($sdk->isError($result)) {
        return $result;
    }
    return ['status' => 204];
});
