<?php

namespace App\Middlewares;

class RequestLoggerMiddleware extends BaseMiddleware
{
    public function handle($request, $next)
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
            $currentDate = date('Y-m-d H:i:s');
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $requestUri = $_SERVER['REQUEST_URI'];
            $logMessage = "[$currentDate] $requestMethod $requestUri\n";

            $this->logRequest($logMessage);
        }

        return $next($request);
    }

    private function logRequest($logMessage)
    {
        $logDirectory = __DIR__ . '/../../logs';
        $logFile = $logDirectory . '/request.log';

        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true); // Create the directory with proper permissions
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
