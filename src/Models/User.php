<?php

namespace App\Models;

class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function create($username, $password): bool {
        $query = "INSERT INTO {$this->table} (username, password) VALUES (:username, :password)";
        $stmt = $this->conn->prepare($query);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashedPassword);

        return $stmt->execute();
    }

    /**
     * @param $username
     * @return array
     */
    public function getByUsername($username): array {
        $query = "SELECT id, username, password FROM {$this->table} WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}