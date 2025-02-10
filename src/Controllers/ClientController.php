<?php

namespace App\Controllers;

use App\Models\Client;
use App\Middleware\AuthMiddleware;

class ClientController
{
    private $db;
    private $client;
    private $auth;

    public function __construct($db)
    {
        $this->db = $db;
        $this->client = new Client($db);
        $this->auth = new AuthMiddleware();
    }

    /**
     * @return array
     */
    public function index(): array
    {
        // Verificar autenticação
        $auth_result = $this->auth->authenticate();
        if ($auth_result['status'] === 'error') {
            return $auth_result;
        }

        try {
            $result = $this->client->list();
            $clients = [];

            while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
                $client = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                ];

                $clients[] = $client;
            }

            return [
                'status' => 'success',
                'data' => $clients
            ];

        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @return array
     */
    public function store(): array
    {
        // Verificar autenticação
        $auth_result = $this->auth->authenticate();
        if ($auth_result['status'] === 'error') {
            return $auth_result;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // Validate request
        $errors = [];
        if (!isset($data['name']) || !filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS)) {
            $errors['name'] = 'Field is required and must be a valid string';
        }
        if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Field is required and must be a valid email';
        }
        if (!isset($data['phone']) || !filter_var($data['phone'], FILTER_SANITIZE_SPECIAL_CHARS)) {
            $errors['phone'] = 'Field is required and must be a valid string';
        }

        if (!empty($errors)) {
            http_response_code(400);
            return [
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $errors
            ];
        }

        try {
            $this->db->beginTransaction();

            // Criar cliente
            $client = $this->client->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone']
            ]);

            if (!$client) {
                throw new \Exception("Error creating client");
            }

            $this->db->commit();

            http_response_code(201);
            return [
                'status' => 'success',
                'message' => 'Client created successfully',
                'data' => $client
            ];

        } catch (\Exception $e) {
            $this->db->rollBack();
            http_response_code(500);
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param int $id
     * @return array|string[]
     */
    public function update(int $id): array
    {
        // Verificar autenticação
        $auth_result = $this->auth->authenticate();
        if ($auth_result['status'] === 'error') {
            return $auth_result;
        }

        // Verificar se o cliente existe
        if (!$this->client->exists($id)) {
            http_response_code(404);
            return [
                'status' => 'error',
                'message' => 'Client not found'
            ];
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // Validação dos campos
        $errors = [];
        if (isset($data['name']) && !filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS)) {
            $errors['name'] = 'Name must be a valid string';
        }
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Must be a valid email';
        }
        if (isset($data['phone']) && !filter_var($data['phone'], FILTER_SANITIZE_SPECIAL_CHARS)) {
            $errors['phone'] = 'Phone must be a valid string';
        }

        if (!empty($errors)) {
            http_response_code(400);
            return [
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $errors
            ];
        }

        try {
            $this->db->beginTransaction();

            // Atualizar cliente
            $client = $this->client->update($id, $data);

            if (!$client) {
                throw new \Exception("Error updating client");
            }

            $this->db->commit();

            return [
                'status' => 'success',
                'message' => 'Client updated successfully',
                'data' => $client
            ];

        } catch (\Exception $e) {
            $this->db->rollBack();
            http_response_code(500);
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param int $id
     * @return array|string[]
     */
    public function delete(int $id): array
    {
        // Verificar autenticação
        $auth_result = $this->auth->authenticate();
        if ($auth_result['status'] === 'error') {
            return $auth_result;
        }

        try {
            if ($this->client->delete($id)) {
                http_response_code(204);
                return [
                    'status' => 'success',
                    'message' => 'Client deleted successfully'
                ];
            }

            throw new \Exception("Error deleting client");

        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}