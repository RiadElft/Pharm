<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;

class Client extends BaseModel
{
    protected string $table = 'clients';

    public function getAll(): array
    {
        $query = "SELECT * FROM {$this->table} ORDER BY nom ASC";
        return $this->db->query($query)->fetchAll();
    }

    public function getAllActifs(): array
    {
        $query = "SELECT * FROM {$this->table} WHERE actif = TRUE ORDER BY nom ASC";
        return $this->db->query($query)->fetchAll();
    }

    public function count(array $conditions = []): int
    {
        try {
            $query = "SELECT COUNT(*) as count FROM {$this->table}";
            
            if (!empty($conditions)) {
                $whereConditions = [];
                foreach ($conditions as $field => $value) {
                    $whereConditions[] = "{$field} = :{$field}";
                }
                $query .= " WHERE " . implode(' AND ', $whereConditions);
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($conditions);
            $result = $stmt->fetch();
            
            return (int) ($result['count'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error in Client::count: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors du comptage des clients");
        }
    }

    public function getById(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $query = "INSERT INTO {$this->table} (nom, telephone, email, adresse, actif)
                  VALUES (:nom, :telephone, :email, :adresse, :actif)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'nom' => $data['nom'],
            'telephone' => $data['telephone'] ?? null,
            'email' => $data['email'] ?? null,
            'adresse' => $data['adresse'] ?? null,
            'actif' => $data['actif'] ?? true
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $query = "UPDATE {$this->table} 
                  SET nom = :nom,
                      telephone = :telephone,
                      email = :email,
                      adresse = :adresse,
                      actif = :actif
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'telephone' => $data['telephone'] ?? null,
            'email' => $data['email'] ?? null,
            'adresse' => $data['adresse'] ?? null,
            'actif' => $data['actif'] ?? true
        ]);
    }

    public function delete(int $id): bool
    {
        // Soft delete by setting actif to false
        $query = "UPDATE {$this->table} SET actif = FALSE WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function hardDelete(int $id): bool
    {
        // Only use this method if you really want to delete the client
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function search(string $term, array $fields = []): array
    {
        // If no fields specified, search in default fields
        if (empty($fields)) {
            $fields = ['nom', 'telephone', 'email'];
        }

        $conditions = array_map(
            fn($field) => "$field LIKE :term",
            $fields
        );
        
        $query = "SELECT * FROM {$this->table} 
                  WHERE " . implode(' OR ', $conditions) . "
                  ORDER BY nom ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['term' => "%$term%"]);
        return $stmt->fetchAll();
    }
} 