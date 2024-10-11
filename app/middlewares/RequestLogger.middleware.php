<?php

namespace App\Middlewares;

class RequestLoggerMiddleware extends BaseMiddleware
{
    public function handle($response = null)
    {
        $currentDate = $this->getCurrentDate();
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $logMessage = "[$currentDate] $requestMethod $requestUri\n";

        $this->logRequest($logMessage);
    }
}
