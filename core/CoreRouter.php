<?php

namespace Core;

use App\Enums\OutputEngineEnum;
use App\Enums\RequestMethodEnum;

class CoreRouter
{
    protected $routeTable;
    protected $globalMiddlewares;
    protected $errorHandlers;
    protected $outputEngine;
    protected $allowedOutputEngines;
    protected $overridedMethods;
    protected $overridedParam;
    protected $sdk;

    public function __construct($sdk)
    {
        $this->sdk = $sdk;
        $this->setInitialValues();
    }

    protected function handleRoute($url, $routes)
    {
        if ($this->matchDynamicRoute($url, $routes)) {
            // Dynamic route matched
        } elseif (isset($routes[$url])) {
            $route = $routes[$url];

            $this->runRouteMiddlewares($route['middlewares'], 'before');
            $response = $this->callRoute($route['callback']);
            $this->runRouteMiddlewares($route['middlewares'], 'after', $response);

            echo $this->formatResponse($response);
        } else {
            echo $this->formatResponse($this->callErrorHandler(404, 'Route not found'));
        }
    }
    protected function runGlobalMiddlewares($timing)
    {
        foreach ($this->globalMiddlewares as $middleware) {
            $instance = new $middleware['callback'][0];

            if ($middleware['before'] && $timing === 'before') {
                call_user_func([$instance, $middleware['callback'][1]]);
            } elseif (!$middleware['before'] && $timing === 'after') {
                call_user_func([$instance, $middleware['callback'][1]]);
            }
        }
    }
    protected function runRouteMiddlewares($middlewares, $timing, $response = null)
    {
        foreach ($middlewares as $middleware) {
            $instance = new $middleware['callback'][0];

            if ($middleware['before'] && $timing === 'before') {
                call_user_func([$instance, $middleware['callback'][1]]);
            } elseif (!$middleware['before'] && $timing === 'after') {
                call_user_func([$instance, $middleware['callback'][1]], $response);
            }
        }
    }
    protected function matchDynamicRoute($url, $routes)
    {
        foreach ($routes as $route => $routeDetails) {
            $pattern = preg_replace('/:\w+/', '(\w+)', $route);
            if (preg_match("#^$pattern$#", $url, $matches)) {
                array_shift($matches);

                $this->runRouteMiddlewares($routeDetails['middlewares'], 'before');
                $response = call_user_func_array($routeDetails['callback'], $matches);
                $this->runRouteMiddlewares($routeDetails['middlewares'], 'after', $response);

                echo $this->formatResponse($response);
                return true;
            }
        }
        return false;
    }
    protected function formatRoute($route)
    {
        return rtrim(ltrim($route, '/'), '/');
    }
    protected function callRoute($callback)
    {
        if (is_callable($callback)) {
            return call_user_func($callback);
        }
        return ['error' => 'Invalid callback'];
    }

    protected function formatResponse($response)
    {
        if (!is_array($response) || !isset($response['status'])) {
            $response = [
                'status' => 200,
                'data' => $response,
            ];
        }

        if (isset($response['status']) && $response['status'] >= 400) {
            if (isset($this->errorHandlers[$response['status']])) {
                $response = call_user_func($this->errorHandlers[$response['status']], $response['message'] ?? null);
            }
        }

        http_response_code($response['status'] ?? 200);

        switch ($this->outputEngine) {
            case OutputEngineEnum::JSON:
                header('Content-Type: application/json');
                return json_encode($response);
            case OutputEngineEnum::XML:
                header('Content-Type: application/xml');
                return $this->arrayToXml($response);
            default:
                return json_encode($response);
        }
    }
    protected function arrayToXml($data, &$xml_data = null)
    {
        if ($xml_data === null) {
            $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subnode = $xml_data->addChild("$key");
                $this->arrayToXml($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }

        return $xml_data->asXML();
    }
    protected function callErrorHandler($errorCode, $message = null)
    {
        if (isset($this->errorHandlers[$errorCode])) {
            return call_user_func($this->errorHandlers[$errorCode], $message);
        }
        return ['error' => "Error $errorCode", 'message' => $message];
    }




    public function get($route, $callback, $middlewares = [])
    {
        $this->routeTable[RequestMethodEnum::GET][$this->formatRoute($route)] = ['callback' => $callback, 'middlewares' => $middlewares];
    }
    public function post($route, $callback, $middlewares = [])
    {
        $this->routeTable[RequestMethodEnum::POST][$this->formatRoute($route)] = ['callback' => $callback, 'middlewares' => $middlewares];
    }
    public function put($route, $callback, $middlewares = [])
    {
        $this->routeTable[RequestMethodEnum::PUT][$this->formatRoute($route)] = ['callback' => $callback, 'middlewares' => $middlewares];
    }
    public function patch($route, $callback, $middlewares = [])
    {
        $this->routeTable[RequestMethodEnum::PATCH][$this->formatRoute($route)] = ['callback' => $callback, 'middlewares' => $middlewares];
    }
    public function delete($route, $callback, $middlewares = [])
    {
        $this->routeTable[RequestMethodEnum::DELETE][$this->formatRoute($route)] = ['callback' => $callback, 'middlewares' => $middlewares];
    }
    public function middleware($middlewareFunc, $timing = 'before')
    {
        $this->globalMiddlewares[] = ['callback' => $middlewareFunc, 'before' => $timing];
    }
    public function CORS()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
    }
    public function bindError($errorCode, $callback)
    {
        $this->errorHandlers[$errorCode] = $callback;
    }

    private function setInitialValues()
    {
        $this->routeTable = [
            RequestMethodEnum::GET => [],
            RequestMethodEnum::POST => [],
            RequestMethodEnum::PUT => [],
            RequestMethodEnum::PATCH => [],
            RequestMethodEnum::DELETE => [],
        ];

        $this->outputEngine = OutputEngineEnum::JSON;
        $this->allowedOutputEngines = OutputEngineEnum::getKeys();
        $this->globalMiddlewares = [];
        $this->errorHandlers = [];
        $this->overridedParam = "_method";
        $this->overridedMethods = ["DELETE", "PUT"];

        if (isset($_GET['format']) && in_array($_GET['format'], $this->allowedOutputEngines)) {
            $this->outputEngine = $_GET['format'];
        }
    }
}
