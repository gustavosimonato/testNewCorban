<?php

use App\Config\Router;
use App\Controllers\AuthController;
use App\Controllers\ClientController;

$router = new Router();


$router->addRoute('POST', 'auth/register', [AuthController::class, 'register']);
$router->addRoute('POST', 'auth/login', [AuthController::class, 'login']);
$router->addRoute('GET', 'clients', [ClientController::class, 'index']);
$router->addRoute('POST', 'clients', [ClientController::class, 'store']);
$router->addRoute('PUT', 'clients/{id}', [ClientController::class, 'update']);
$router->addRoute('DELETE', 'clients/{id}', [ClientController::class, 'delete']);

return $router;