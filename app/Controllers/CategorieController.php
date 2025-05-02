<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Categorie;
use PDOException;

class CategorieController
{
    private Categorie $categorieModel;

    public function __construct()
    {
        $this->categorieModel = new Categorie();
    }

    public function index()
    {
        try {
            $categories = $this->categorieModel->getAllActives();
            require __DIR__ . '/../Views/categories/index.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Une erreur est survenue lors de la récupération des catégories.';
            error_log('Error in CategorieController::index: ' . $e->getMessage());
            header('Location: /dashboard');
            exit;
        }
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validate required fields
                if (empty($_POST['nom'])) {
                    $_SESSION['error'] = 'Le nom de la catégorie est requis.';
                    require __DIR__ . '/../Views/categories/create.php';
                    return;
                }

                $data = [
                    'nom' => $_POST['nom'],
                    'description' => $_POST['description'] ?? '',
                    'actif' => true
                ];

                $this->categorieModel->create($data);
                $_SESSION['success'] = 'Catégorie créée avec succès.';
                header('Location: /categories');
                exit;
            }
            require __DIR__ . '/../Views/categories/create.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Une erreur est survenue lors de la création de la catégorie.';
            error_log('Error in CategorieController::create: ' . $e->getMessage());
            require __DIR__ . '/../Views/categories/create.php';
        }
    }

    public function edit($id)
    {
        try {
            $category = $this->categorieModel->findById($id);
            if (!$category) {
                $_SESSION['error'] = 'Catégorie non trouvée.';
                header('Location: /categories');
                exit;
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validate required fields
                if (empty($_POST['nom'])) {
                    $_SESSION['error'] = 'Le nom de la catégorie est requis.';
                    require __DIR__ . '/../Views/categories/edit.php';
                    return;
                }

                $data = [
                    'nom' => $_POST['nom'],
                    'description' => $_POST['description'] ?? '',
                    'actif' => isset($_POST['actif']) ? (bool)$_POST['actif'] : true
                ];
                
                $this->categorieModel->update($id, $data);
                $_SESSION['success'] = 'Catégorie mise à jour avec succès.';
                header('Location: /categories');
                exit;
            }

            // Pass the category data to the view
            $data = ['category' => $category];
            require __DIR__ . '/../Views/categories/edit.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Une erreur est survenue lors de la modification de la catégorie.';
            error_log('Error in CategorieController::edit: ' . $e->getMessage());
            header('Location: /categories');
            exit;
        }
    }

    public function delete($id)
    {
        try {
            $this->categorieModel->delete($id);
            $_SESSION['success'] = 'Catégorie supprimée avec succès.';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Une erreur est survenue lors de la suppression de la catégorie.';
            error_log('Error in CategorieController::delete: ' . $e->getMessage());
        }
        header('Location: /categories');
        exit;
    }
} 