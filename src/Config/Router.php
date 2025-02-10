<?php

namespace App\Config;

class Router
{
    private $routes = [];

    /**
     * @param string $method
     * @param string $uri
     * @param array $action
     * @return void
     */
    public function addRoute(string $method, string $uri, array $action): void
    {
        // Converte o padrão da rota em uma expressão regular
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $uri);
        $pattern = "@^" . $pattern . "$@D";
        
        $this->routes[$method][$uri] = [
            'pattern' => $pattern,
            'action' => $action
        ];
    }

    /**
     * @param string $method
     * @param string $uri
     * @return array|null
     */
    public function getRoute(string $method, string $uri): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        // Procura por uma rota correspondente
        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $uri, $params)) {
                // Remove índices numéricos
                $params = array_filter($params, 'is_string', ARRAY_FILTER_USE_KEY);
                
                return [
                    'action' => $route['action'],
                    'params' => $params
                ];
            }
        }

        return null;
    }
}