<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../core/autoload.php';

use Core\Router;

$router = new Router();
require __DIR__ . '/router.rules.php';
$router->dispatch($_SERVER['REQUEST_URI']);
