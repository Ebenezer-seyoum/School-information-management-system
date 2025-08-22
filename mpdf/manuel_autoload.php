<?php
// file: mpdf/manual_autoload.php

spl_autoload_register(function ($class) {
    $prefix = 'Mpdf\\';
    $base_dir = __DIR__ . '/src/';

    // Only handle Mpdf namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get relative class name
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
