<?php

namespace Core;

use App\Enums\RequestMethodEnum;

class Router extends CoreRouter
{

    public function dispatch()
    {
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

        if (RequestMethodEnum::isValidKey($method)) {
            $url = $this->formatRoute(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            $this->handleRoute($url, $this->routeTable[$method]);
        } else {
            echo $this->formatResponse($this->callErrorHandler(405, 'Method not supported'));
        }

        $this->runGlobalMiddlewares('before');
    }

    public function sdk() {
        return $this->sdk;
    }
}
