<?php

namespace App\Middlewares;

class RequestLoggerMiddleware extends BaseMiddleware
{
    public function handle($response = null)
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
            $currentDate = $this->getCurrentDate();
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $requestUri = $_SERVER['REQUEST_URI'];
            $logMessage = "[$currentDate] $requestMethod $requestUri\n";

            $this->logRequest($logMessage);
        }

        // return $next($request);
    }
}
