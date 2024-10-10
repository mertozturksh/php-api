<?php

function requestLoggerMiddleware()
{
    $logFile = __DIR__ . '/../../logs/request.log';
    $currentDate = date('Y-m-d H:i:s');
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestUri = $_SERVER['REQUEST_URI'];

    $logMessage = "[$currentDate] $requestMethod $requestUri\n";

    file_put_contents($logFile, $logMessage, FILE_APPEND);
}