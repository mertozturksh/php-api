<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../core/autoload.php';

use Core\Router;
use Core\SDK;

$sdk = new SDK();
$router = new Router($sdk);
require __DIR__ . '/router.errors.php';
require __DIR__ . '/router.rules.php';
$router->dispatch();
