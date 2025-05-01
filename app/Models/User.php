<?php

declare(strict_types=1);

namespace App\Models;

class User extends BaseModel
{
    protected string $table = 'utilisateurs';

    /**
     * Find a user by email
     */
    public function findByEmail(string $email): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE email = :email AND actif = TRUE LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get user with role name (join with roles table)
     */
    public function findByIdWithRole(int $id): ?array
    {
        $query = "SELECT u.*, r.nom as role FROM {$this->table} u 
                 LEFT JOIN roles r ON u.role_id = r.id 
                 WHERE u.id = :id AND u.actif = TRUE LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get all users with their role names
     */
    public function all(array $conditions = [], array $orderBy = []): array
    {
        $query = "SELECT u.*, r.nom as role FROM {$this->table} u 
                 LEFT JOIN roles r ON u.role_id = r.id 
                 WHERE u.actif = TRUE";
                 
        if (!empty($orderBy)) {
            $query .= " ORDER BY " . implode(', ', array_map(
                fn($key, $direction) => "u.$key $direction",
                array_keys($orderBy),
                $orderBy
            ));
        } else {
            $query .= " ORDER BY u.nom ASC";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Create a new user with hashed password
     */
    public function createUser(array $data): int
    {
        // Get role ID from role name if provided as string
        if (isset($data['role']) && is_string($data['role'])) {
            $role = $this->findRoleByName($data['role']);
            $data['role_id'] = $role ? $role['id'] : 1; // Default to role ID 1 if not found
            unset($data['role']);
        }
        
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        return $this->create($data);
    }

    /**
     * Update user password
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        return $this->update($userId, [
            'mot_de_passe' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Deactivate a user
     */
    public function deactivate(int $userId): bool
    {
        return $this->update($userId, ['actif' => false]);
    }

    /**
     * Get active users count
     */
    public function getActiveCount(): int
    {
        return $this->count(['actif' => true]);
    }
    
    /**
     * Find a role by name
     */
    private function findRoleByName(string $roleName): ?array
    {
        $query = "SELECT * FROM roles WHERE nom = :nom LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['nom' => $roleName]);
        return $stmt->fetch() ?: null;
    }
} 