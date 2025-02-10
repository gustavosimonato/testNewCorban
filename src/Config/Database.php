<?php

namespace App\Config;

class Database
{
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private $conn = null;

    public function __construct()
    {
        $envPath = __DIR__ . '/../../.env';
        $env = parse_ini_file($envPath);
        if ($env === false) {
            throw new \Exception("Failed to open .env file");
        }
        $this->host = $env['DB_HOST'];
        $this->db_name = $env['DB_NAME'];
        $this->username = $env['DB_USER'];
        $this->password = $env['DB_PASS'];
    }

    /**
     * @return \PDO
     * @throws \Exception
     */
    public function getConnection(): \PDO
    {
        try {
            if ($this->conn === null) {
                $this->conn = new \PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
            return $this->conn;
        } catch (\PDOException $e) {
            throw new \Exception("Connection error: " . $e->getMessage());
        }
    }
}