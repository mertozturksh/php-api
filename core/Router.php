<?php

namespace Core;

use App\Enums\RequestMethodEnum;

class Router extends CoreRouter
{

    public function dispatch($url)
    {
        $url = parse_url($url, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($_REQUEST[$this->overridedParam]) && in_array(strtoupper($_REQUEST[$this->overridedParam]), $this->overridedMethods)) {
            $method = strtoupper($_REQUEST[$this->overridedParam]);
        }

        if ($method === 'OPTIONS') {
            $this->CORS();
            echo $this->formatResponse(['message' => 'CORS preflight']);
            exit();
        }

        $this->runGlobalMiddlewares('before');

        $url = $this->formatRoute($url);
        switch ($method) {
            case RequestMethodEnum::GET:
                $this->handleRoute($url, $this->routeTable[RequestMethodEnum::GET]);
                break;
            case RequestMethodEnum::POST:
                $this->handleRoute($url, $this->routeTable[RequestMethodEnum::POST]);
                break;
            case RequestMethodEnum::PUT:
                $this->handleRoute($url, $this->routeTable[RequestMethodEnum::PUT]);
                break;
            case RequestMethodEnum::PATCH:
                $this->handleRoute($url, $this->routeTable[RequestMethodEnum::PATCH]);
                break;
            case RequestMethodEnum::DELETE:
                $this->handleRoute($url, $this->routeTable[RequestMethodEnum::DELETE]);
                break;
            default:
                echo $this->formatResponse($this->callErrorHandler(405, 'Method not supported'));
                break;
        }

        $this->runGlobalMiddlewares('before');
    }

}
