<?php

namespace Core\Middlewares;

class RequestLogger
{

    public static function log()
    {
        $logFile = __DIR__ . '/../../logs/request.log';
        $currentDate = date('Y-m-d H:i:s');
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];

        $logMessage = "[$currentDate] $requestMethod $requestUri\n";

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public static function logResponse($response)
    {
        if (isset($response['status']) && $response['status'] >= 400) {
            error_log("Hata oluştu: " . json_encode($response));
        } else {
            error_log("Başarılı istek: " . json_encode($response));
        }
    }
}
