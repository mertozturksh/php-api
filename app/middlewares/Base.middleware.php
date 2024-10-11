<?php

namespace App\Middlewares;

abstract class BaseMiddleware
{
    abstract public function handle($response = null);

    protected function logRequest($logMessage)
    {
        $logFile = __DIR__ . '/../../logs/request.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    protected function getCurrentDate()
    {
        return date('Y-m-d H:i:s');
    }
}
