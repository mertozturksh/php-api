<?php

function logResponseMiddleware($response)
{
    if (isset($response['status']) && $response['status'] >= 400) {
        error_log("Hata oluştu: " . json_encode($response));
    } else {
        error_log("Başarılı istek: " . json_encode($response));
    }
}