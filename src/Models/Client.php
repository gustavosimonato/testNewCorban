<?php

namespace App\Models;

class Client
{
    private $conn;
    private $table = 'clients';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * @return \PDOStatement
     */
    public function list(): \PDOStatement
    {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    public function getById(int $id)
    {
        $query = "SELECT id, name, email, phone FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param array $data
     * @return array|false
     */
    public function create(array $data): array|false
    {
        $query = "INSERT INTO {$this->table} (name, email, phone) VALUES (:name, :email, :phone)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":phone", $data['phone']);

        if ($stmt->execute()) {
            $lastInsertId = $this->conn->lastInsertId();
            return $this->getById($lastInsertId);
        }
        return false;
    }


    /**
     * @param int $id
     * @param array $data
     * @return array|false
     */
    public function update(int $id, array $data): array|false
    {
        $query = "UPDATE {$this->table}
                 SET name = :name,
                     email = :email,
                     phone = :phone
                 WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return $this->getById($id);
        }
        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return (bool)$stmt->fetchColumn();
    }
}