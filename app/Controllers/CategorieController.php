<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Categorie;

class CategorieController
{
    private Categorie $categorieModel;

    public function __construct()
    {
        $this->categorieModel = new Categorie();
    }

    public function index()
    {
        $categories = $this->categorieModel->getAllActives();
        require __DIR__ . '/../Views/categories/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'],
                'description' => $_POST['description'] ?? '',
                'actif' => true
            ];
            $this->categorieModel->create($data);
            header('Location: /categories');
            exit;
        }
        require __DIR__ . '/../Views/categories/create.php';
    }

    public function edit($id)
    {
        $categorie = $this->categorieModel->findById($id);
        if (!$categorie) {
            $_SESSION['error'] = 'Catégorie non trouvée.';
            header('Location: /categories');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'],
                'description' => $_POST['description'] ?? '',
                'actif' => isset($_POST['actif']) ? (bool)$_POST['actif'] : true
            ];
            $this->categorieModel->update($id, $data);
            header('Location: /categories');
            exit;
        }
        require __DIR__ . '/../Views/categories/edit.php';
    }

    public function delete($id)
    {
        $this->categorieModel->delete($id);
        header('Location: /categories');
        exit;
    }
} 