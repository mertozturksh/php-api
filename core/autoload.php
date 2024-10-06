<?php

spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\Controllers\\' => 'controllers',
        'App\\Enums\\' => 'enums',
        'App\\Models\\' => 'models'
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

            $file = __DIR__ . '/../app/' . $directory . '/' . $className . $suffix;

            if (file_exists($file)) {
                require $file;
            } else {
                echo "Dosya bulunamadı: " . $file;
                exit;
            }
        }
    }
});
