<?php

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Router;

$router = new Router();
require APP_PATH . '/routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($method, $path);