<?php

namespace App\Middlewares;

abstract class BaseMiddleware
{
    abstract public function handle($request, $next);


    
    protected function logRequest($logMessage)
    {
        $logDirectory = __DIR__ . '/../../logs';
        $logFile = $logDirectory . '/request.log';

        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true); // Create the directory with proper permissions
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    protected function getCurrentDate()
    {
        return date('Y-m-d H:i:s');
    }
}
