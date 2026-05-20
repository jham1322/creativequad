<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Support hosts where the public web root is separated from the Laravel app
// or where the app folder sits inside public_html.
$appBasePath = is_dir(__DIR__.'/laravel_app')
    ? __DIR__.'/laravel_app'
    : (is_dir(__DIR__.'/../laravel_app')
        ? __DIR__.'/../laravel_app'
        : __DIR__.'/..');

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $appBasePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $appBasePath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appBasePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
