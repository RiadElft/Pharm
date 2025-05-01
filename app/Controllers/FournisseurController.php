<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Fournisseur;

class FournisseurController
{
    private Fournisseur $fournisseurModel;

    public function __construct()
    {
        $this->fournisseurModel = new Fournisseur();
    }

    public function index()
    {
        $fournisseurs = $this->fournisseurModel->getAllActifs();
        require __DIR__ . '/../Views/fournisseurs/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'],
                'contact' => $_POST['contact'] ?? '',
                'email' => $_POST['email'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'adresse' => $_POST['adresse'] ?? '',
                'actif' => true
            ];
            $this->fournisseurModel->create($data);
            header('Location: /fournisseurs');
            exit;
        }
        require __DIR__ . '/../Views/fournisseurs/create.php';
    }

    public function edit($id)
    {
        $fournisseur = $this->fournisseurModel->findById($id);
        if (!$fournisseur) {
            $_SESSION['error'] = 'Fournisseur non trouvé.';
            header('Location: /fournisseurs');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'],
                'contact' => $_POST['contact'] ?? '',
                'email' => $_POST['email'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'adresse' => $_POST['adresse'] ?? '',
                'actif' => isset($_POST['actif']) ? (bool)$_POST['actif'] : true
            ];
            $this->fournisseurModel->update($id, $data);
            header('Location: /fournisseurs');
            exit;
        }
        require __DIR__ . '/../Views/fournisseurs/edit.php';
    }

    public function delete($id)
    {
        $this->fournisseurModel->delete($id);
        header('Location: /fournisseurs');
        exit;
    }
} 