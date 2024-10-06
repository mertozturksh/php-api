<?php

class Router
{
    private $getRoutes = [];
    private $postRoutes = [];
    private $putRoutes = [];
    private $deleteRoutes = [];

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
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $this->formatRoute($url);

        switch ($method) {
            case 'GET':
                if (isset($this->getRoutes[$url])) {
                    echo json_encode($this->callRoute($this->getRoutes[$url]));
                } else {
                    echo json_encode(['error' => 'Route not found']);
                }
                break;

            case 'POST':
                if (isset($this->postRoutes[$url])) {
                    echo json_encode($this->callRoute($this->postRoutes[$url]));
                } else {
                    echo json_encode(['error' => 'Route not found']);
                }
                break;

            case 'PUT':
                if (isset($this->putRoutes[$url])) {
                    echo json_encode($this->callRoute($this->putRoutes[$url]));
                } else {
                    echo json_encode(['error' => 'Route not found']);
                }
                break;

            case 'DELETE':
                if (isset($this->deleteRoutes[$url])) {
                    echo json_encode($this->callRoute($this->deleteRoutes[$url]));
                } else {
                    echo json_encode(['error' => 'Route not found']);
                }
                break;

            default:
                echo json_encode(['error' => 'Method not supported']);
                break;
        }
    }

    private function formatRoute($route)
    {
        return ltrim($route, '/');
    }

    private function callRoute($callback)
    {
        if (is_callable($callback)) {
            return call_user_func($callback);
        }

        return ['error' => 'Invalid callback'];
    }
}
