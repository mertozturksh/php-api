<?php

namespace Core;

class SDK
{
    private $dbInstance;
    private $controllersNamespace = 'App\\Controllers\\';
    private $controllersPath = __DIR__ . '/../app/controllers/';

    public function __construct()
    {
        $this->dbInstance = new Database(__DIR__ . '/../config/database.ini');
    }


    public function db()
    {
        return $this->dbInstance;
    }

    public function controller($controllerName)
    {
        $controllerFile = $this->controllersPath . $controllerName . '.controller.php';
        $controllerClass = $this->controllersNamespace . $controllerName . 'Controller';

        if (file_exists($controllerFile) && class_exists($controllerClass)) {
            return new $controllerClass($this);
        }
        throw new \Exception("Controller $controllerClass not found.");
    }
}
