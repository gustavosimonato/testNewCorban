<?php
// src/Controllers/AuthController.php

namespace App\Controllers;

use App\Models\User;
use App\Middleware\AuthMiddleware;

class AuthController
{
    private $db;
    private $user;
    private $auth;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new User($db);
        $this->auth = new AuthMiddleware();
    }

    /**
     * @return string[]|void
     */
    public function register(): array
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            return [
                'status' => 'error',
                'message' => 'Missing required fields'
            ];
        }

        try {
            if ($this->user->create($data['username'], $data['password'])) {
                http_response_code(201);
                return [
                    'status' => 'success',
                    'message' => 'User created successfully'
                ];
            }
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'status' => 'error',
                'message' => 'Error creating user'
            ];
        }
    }

    /**
     * @return array|string[]
     */
    public function login(): array
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            return [
                'status' => 'error',
                'message' => 'Missing required fields'
            ];
        }

        $user = $this->user->getByUsername($data['username']);

        if ($user && password_verify($data['password'], $user['password'])) {
            $token = $this->auth->generateToken($user);

            return [
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token
            ];
        }

        http_response_code(401);
        return [
            'status' => 'error',
            'message' => 'Invalid credentials'
        ];
    }
}