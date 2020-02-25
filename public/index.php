<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$_ENV['APP_DIR'] = dirname(__DIR__);

use App\Kernel\Router;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
$router = new Router($request);
$router->run();