<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $secret_key = "abc123";


    /**
     * @return array|string[]
     */
    public function authenticate(): array {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            return [
                'status' => 'error',
                'message' => 'No token provided'
            ];
        }

        $jwt = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            $decoded = JWT::decode($jwt, new Key($this->secret_key, 'HS256'));
            return [
                'status' => 'success',
                'user' => $decoded
            ];
        } catch (\Exception $e) {
            http_response_code(401);
            return [
                'status' => 'error',
                'message' => 'Invalid token'
            ];
        }
    }

    /**
     * @param $user
     * @return string
     */
    public function generateToken($user): string {
        $payload = [
            'iss' => 'localhost',
            'aud' => 'localhost',
            'iat' => time(),
            'exp' => time() + (60 * 60), // Token válido por 1 hora
            'data' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ];

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }
}