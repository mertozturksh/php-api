<?php

namespace Core;

class Router
{
    private $getRoutes = [];
    private $postRoutes = [];
    private $putRoutes = [];
    private $deleteRoutes = [];
    protected $middlewares = [];
    protected $overridedMethods = ["DELETE", "PUT"];
    protected $overridedParam = "_method";
    protected $outputEngine = 'json';
    protected $allowedOutputEngines = ["json", "xml"];
    protected $errorHandlers = [];
    public function __construct()
    {
        $this->checkOutputFormat();
    }

    public function get($route, $callback)
    {
        $this->getRoutes[$this->formatRoute($route)] = $callback;
    }
    public function post($route, $callback)
    {
        $this->postRoutes[$this->formatRoute($route)] = $callback;
    }
    public function put($route, $callback)
    {
        $this->putRoutes[$this->formatRoute($route)] = $callback;
    }
    public function delete($route, $callback)
    {
        $this->deleteRoutes[$this->formatRoute($route)] = $callback;
    }

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

        foreach ($this->middlewares as $middleware) {
            call_user_func($middleware);
        }

        $url = $this->formatRoute($url);

        switch ($method) {
            case 'GET':
                $this->handleRoute($url, $this->getRoutes);
                break;
            case 'POST':
                $this->handleRoute($url, $this->postRoutes);
                break;
            case 'PUT':
                $this->handleRoute($url, $this->putRoutes);
                break;
            case 'DELETE':
                $this->handleRoute($url, $this->deleteRoutes);
                break;
            default:
                echo $this->formatResponse($this->callErrorHandler(405, 'Method not supported'));
                break;
        }
    }


    private function handleRoute($url, $routes)
    {
        if (isset($routes[$url])) {
            echo $this->formatResponse($this->callRoute($routes[$url]));
        } elseif ($this->matchDynamicRoute($url, $routes)) {
        } else {
            echo $this->formatResponse($this->callErrorHandler(404, 'Route not found'));
        }
    }

    public function matchDynamicRoute($url, $routes)
    {
        foreach ($routes as $route => $callback) {
            $pattern = preg_replace('/:\w+/', '(\w+)', $route);
            if (preg_match("#^$pattern$#", $url, $matches)) {
                array_shift($matches);
                echo $this->formatResponse(call_user_func_array($callback, $matches));
                return true;
            }
        }
        return false;
    }

    private function formatRoute($route)
    {
        return rtrim(ltrim($route, '/'), '/');
    }

    private function callRoute($callback)
    {
        if (is_callable($callback)) {
            return call_user_func($callback);
        }
        return ['error' => 'Invalid callback'];
    }

    public function CORS()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
    }

    public function middleware($middlewareFunc)
    {
        $this->middlewares[] = $middlewareFunc;
    }

    private function checkOutputFormat()
    {
        if (isset($_GET['format']) && in_array($_GET['format'], $this->allowedOutputEngines)) {
            $this->outputEngine = $_GET['format'];
        }
    }

    private function formatResponse($response)
    {
        if (isset($response['status'])) {
            http_response_code($response['status']);
        } else {
            http_response_code(200);
        }

        if (isset($response['status']) && $response['status'] >= 400) {
            if (isset($this->errorHandlers[$response['status']])) {
                $response = call_user_func($this->errorHandlers[$response['status']], $response['message'] ?? null);
            }
        }

        switch ($this->outputEngine) {
            case 'json':
                header('Content-Type: application/json');
                return json_encode($response);
            case 'xml':
                header('Content-Type: application/xml');
                return $this->arrayToXml($response);
            default:
                return json_encode($response);
        }
    }



    private function arrayToXml($data, &$xml_data = null)
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

    public function bindError($errorCode, $callback)
    {
        $this->errorHandlers[$errorCode] = $callback;
    }

    private function callErrorHandler($errorCode, $message = null)
    {
        if (isset($this->errorHandlers[$errorCode])) {
            return call_user_func($this->errorHandlers[$errorCode], $message);
        }
        return ['error' => "Error $errorCode", 'message' => $message];
    }
}
