<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;

//mostre os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Parse request
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Load routes
$router = require __DIR__ . '/../src/routes.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Find the route
    $routeInfo = $router->getRoute($method, $uri);
    if ($routeInfo) {
        [$controllerClass, $action] = $routeInfo['action'];
        $controller = new $controllerClass($conn);
        
        // Passa os parâmetros da rota para o método do controller
        $params = $routeInfo['params'] ?? [];
        $response = $controller->$action(...array_values($params));
    } else {
        http_response_code(404);
        $response = ['status' => 'error', 'message' => 'Route not found'];
    }

} catch (Exception $e) {
    http_response_code(500);
    $response = [
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ];
}

// Send response
echo json_encode($response);