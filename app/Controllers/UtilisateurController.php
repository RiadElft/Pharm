<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Config\Database;

final class UtilisateurController
{
    private User $userModel;
    private \PDO $db;

    public function __construct()
    {
        $this->userModel = new User();
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $users = $this->userModel->all();
        require __DIR__ . '/../Views/utilisateurs/index.php';
    }

    public function create(): void
    {
        // Get roles for the dropdown
        $roles = $this->getRoles();
        require __DIR__ . '/../Views/utilisateurs/create.php';
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom'           => $_POST['nom'],
                'email'         => $_POST['email'],
                'role'          => $_POST['role'],
                'mot_de_passe'  => $_POST['password'],
                'actif'         => true,
                'cree_le'       => date('Y-m-d H:i:s'),
                'modifie_le'    => date('Y-m-d H:i:s')
            ];
            $this->userModel->createUser($data);
            $_SESSION['success'] = 'Utilisateur créé avec succès.';
            header('Location: /utilisateurs');
            exit;
        }
        header('Location: /utilisateurs/create');
        exit;
    }

    public function edit(int $id): void
    {
        $user = $this->userModel->findByIdWithRole($id);
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé.';
            header('Location: /utilisateurs');
            exit;
        }
        
        // Get roles for the dropdown
        $roles = $this->getRoles();
        require __DIR__ . '/../Views/utilisateurs/edit.php';
    }
    
    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->userModel->findById($id);
            if (!$user) {
                $_SESSION['error'] = 'Utilisateur non trouvé.';
                header('Location: /utilisateurs');
                exit;
            }
            
            // Get role ID from name
            $role = $this->findRoleByName($_POST['role']);
            $roleId = $role ? $role['id'] : 1; // Default to 1 if not found
            
            $data = [
                'nom'           => $_POST['nom'],
                'email'         => $_POST['email'],
                'role_id'       => $roleId,
                'actif'         => isset($_POST['actif']) ? (bool)$_POST['actif'] : true,
                'modifie_le'    => date('Y-m-d H:i:s')
            ];
            
            if (!empty($_POST['password'])) {
                $data['mot_de_passe'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            $this->userModel->update($id, $data);
            $_SESSION['success'] = 'Utilisateur mis à jour avec succès.';
            header('Location: /utilisateurs');
            exit;
        }
        
        header('Location: /utilisateurs/edit/' . $id);
        exit;
    }

    public function delete(int $id): void
    {
        $this->userModel->deactivate($id);
        $_SESSION['success'] = 'Utilisateur supprimé avec succès.';
        header('Location: /utilisateurs');
        exit;
    }
    
    /**
     * Get all available roles
     */
    private function getRoles(): array
    {
        $query = "SELECT * FROM roles ORDER BY nom ASC";
        return $this->db->query($query)->fetchAll();
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