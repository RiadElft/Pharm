<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

abstract class BaseModel implements ModelInterface
{
    public PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM {$this->table}";
        return $this->db->query($query)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch() ?: null;
    }

    /**
     * Get all records from the table
     */
    public function all(array $conditions = [], array $orderBy = []): array
    {
        $query = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', array_map(
                fn($key) => "$key = :$key",
                array_keys($conditions)
            ));
        }

        if (!empty($orderBy)) {
            $query .= " ORDER BY " . implode(', ', array_map(
                fn($key, $direction) => "$key $direction",
                array_keys($orderBy),
                $orderBy
            ));
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($conditions);
        
        return $stmt->fetchAll();
    }

    /**
     * Create a new record
     */
    public function create(array $data): int
    {
        $fields = array_keys($data);
        $values = array_map(fn($field) => ":{$field}", $fields);
        
        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $fields),
            implode(', ', $values)
        );
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($data);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing record
     */
    public function update(int $id, array $data): bool
    {
        $fields = array_map(
            fn($field) => "{$field} = :{$field}",
            array_keys($data)
        );
        
        $query = sprintf(
            "UPDATE %s SET %s WHERE id = :id",
            $this->table,
            implode(', ', $fields)
        );
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([...$data, 'id' => $id]);
    }

    /**
     * Delete a record
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Search records
     */
    public function search(string $term, array $fields = []): array
    {
        if (empty($fields)) {
            return [];
        }

        $conditions = array_map(
            fn($field) => "$field LIKE :term",
            $fields
        );
        
        $query = sprintf(
            "SELECT * FROM %s WHERE %s",
            $this->table,
            implode(' OR ', $conditions)
        );
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['term' => "%$term%"]);
        
        return $stmt->fetchAll();
    }

    /**
     * Count records with optional conditions
     */
    public function count(array $conditions = []): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($conditions)) {
            $conditions = array_map(
                fn($field) => "{$field} = :{$field}",
                array_keys($conditions)
            );
            $query .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($conditions);
        $result = $stmt->fetch();
        
        return (int) ($result['count'] ?? 0);
    }

    protected function findBy(array $criteria): array
    {
        $conditions = array_map(
            fn($field) => "{$field} = :{$field}",
            array_keys($criteria)
        );
        
        $query = sprintf(
            "SELECT * FROM %s WHERE %s",
            $this->table,
            implode(' AND ', $conditions)
        );
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($criteria);
        
        return $stmt->fetchAll();
    }
} 