<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
$dependencies = require __DIR__ . '/../src/dependencies.php';
$dependencies($app);

// Register middleware
$middleware = require __DIR__ . '/../src/middleware.php';
$middleware($app);

// Register routes_obat
$routes_obat = require __DIR__ . '/../src/routes/routes_obat.php';
$routes_obat($app);

// Register routes_obat
$routes_penyakit = require __DIR__ . '/../src/routes/routes_penyakit.php';
$routes_penyakit($app);

// Register Routes Auth
$routes_auth = require __DIR__ . '/../src/routes/routes_auth.php';
$routes_auth($app);

// Register Routes View
$routes_view = require __DIR__ . '/../src/routes/routes_view.php';
$routes_view($app);

// Run app
$app->run();
