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
    public function __destruct()
    {
        $this->dbInstance->disconnect();
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

    public function checkVariables($variables, $fields) {
        foreach ($fields as $field) {
            if(!isset($variables[$field]) || empty($variables[$field])) {
                return false;
            }
        }
        return true;
    }

    public function isError($data) {
        if (is_array($data) && isset($data['status']) && $data['status'] >= 400) {
            return true;
        }
        return false;
    }
}
