<?php

spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\Controllers\\' => 'controllers',
        'App\\Enums\\' => 'enums',
        'App\\Models\\' => 'models',
        'App\\Middlewares\\' => 'middlewares',
        'App\\Exceptions\\' => 'exceptions'
    ];

    foreach ($prefixes as $prefix => $directory) {
        if (strpos($class, $prefix) === 0) {
            $className = str_replace($prefix, '', $class);

            if ($directory === 'controllers') {
                $className = str_replace('Controller', '', $className);
                $suffix = '.controller.php';
            } elseif ($directory === 'enums') {
                $className = str_replace('Enum', '', $className);
                $suffix = '.enum.php';
            } elseif ($directory === 'models') {
                $className = str_replace('Model', '', $className);
                $suffix = '.model.php';
            }
            elseif ($directory === 'middlewares') {
                $className = str_replace('Middleware', '', $className);
                $suffix = '.middleware.php';
            }
            elseif ($directory === 'exceptions') {
                $className = str_replace('Exception', '', $className);
                $suffix = '.exception.php';
            }

            $file = __DIR__ . '/../app/' . $directory . '/' . $className . $suffix;

            if (file_exists($file)) {
                require $file;
            } else {
                echo "File could not find: " . $file;
                exit;
            }
        }
    }
});
