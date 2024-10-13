<?php

// 400 Bad Request
$router->bindError(400, function ($message) {
    return [
        "status" => 400,
        "message" => $message ?? "Bad request."
    ];
});

// 401 Unauthorized 
$router->bindError(401, function ($message) {
    return [
        "status" => 401,
        "message" => $message ?? "You're not authorized."
    ];
});

// 403 Forbidden
$router->bindError(403, function ($message) {
    return [
        "status" => 403,
        "message" => $message ?? "Forbidden to access this resource."
    ];
});

// 404 Not Found
$router->bindError(404, function ($message) {
    return [
        "status" => 404,
        "message" => $message ?? "The requested URL was not found."
    ];
});

// 405 Not Found
$router->bindError(405, function ($message) {
    return [
        "status" => 405,
        "message" => $message ?? "Request method not allowed."
    ];
});

// 500 Internal Server Error
$router->bindError(500, function ($message) {
    return [
        "status" => 500,
        "message" => $message ?? "The server encountered an internal error."
    ];
});
