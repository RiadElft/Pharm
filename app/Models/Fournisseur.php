<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Fournisseur extends BaseModel
{
    protected string $table = 'fournisseurs';

    public function getAllActifs(): array
    {
        $query = "SELECT * FROM {$this->table} WHERE actif = TRUE";
        return $this->db->query($query)->fetchAll();
    }

    public function create(array $data): int
    {
        $query = "INSERT INTO {$this->table} (nom, contact, email, telephone, adresse, actif)
                  VALUES (:nom, :contact, :email, :telephone, :adresse, :actif)";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'nom' => $data['nom'],
            'contact' => $data['contact'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse'],
            'actif' => $data['actif']
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $query = "UPDATE {$this->table} 
                  SET nom = :nom, 
                      contact = :contact, 
                      email = :email, 
                      telephone = :telephone, 
                      adresse = :adresse, 
                      actif = :actif
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'contact' => $data['contact'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse'],
            'actif' => $data['actif']
        ]);
    }

    public function delete(int $id): bool
    {
        $query = "UPDATE {$this->table} SET actif = FALSE WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
} 