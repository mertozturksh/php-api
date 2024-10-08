<?php

// 400 Bad Request
$router->bindError(400, function ($message) {
    return [
        "responseCode" => 400,
        "message" => $message ?? "Bad request."
    ];
});

// 401 Unauthorized 
$router->bindError(401, function ($message) {
    return [
        "responseCode" => 401,
        "message" => $message ?? "You're not authorized."
    ];
});

// 403 Forbidden
$router->bindError(403, function ($message) {
    return [
        "responseCode" => 403,
        "message" => $message ?? "Forbidden to access this resource."
    ];
});

// 404 Not Found
$router->bindError(404, function ($message) {
    return [
        "responseCode" => 404,
        "message" => $message ?? "The requested URL was not found."
    ];
});

// 500 Internal Server Error
$router->bindError(500, function ($message) {
    return [
        "responseCode" => 500,
        "message" => $message ?? "The server encountered an internal error."
    ];
});
