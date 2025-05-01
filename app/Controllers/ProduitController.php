<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Fournisseur;

class ProduitController
{
    private Produit $produitModel;
    private Categorie $categorieModel;
    private Fournisseur $fournisseurModel;

    public function __construct()
    {
        $this->produitModel = new Produit();
        $this->categorieModel = new Categorie();
        $this->fournisseurModel = new Fournisseur();
    }

    public function index()
    {
        $Produits = $this->produitModel->getAllActifs();
        $categories = $this->categorieModel->getAllActives();
        $fournisseurs = $this->fournisseurModel->getAllActifs();
        
        // Get category and supplier names
        foreach ($Produits as &$produit) {
            if (isset($produit['categorie_id'])) {
                $categorie = $this->categorieModel->findById($produit['categorie_id']);
                $produit['categorie_nom'] = $categorie ? $categorie['nom'] : 'N/A';
            }
            
            if (isset($produit['fournisseur_id'])) {
                $fournisseur = $this->fournisseurModel->findById($produit['fournisseur_id']);
                $produit['fournisseur_nom'] = $fournisseur ? $fournisseur['nom'] : 'N/A';
            }
        }
        unset($produit); // Break the reference
        
        // Set active menu item
        $path = 'Produits';
        
        require __DIR__ . '/../Views/Produits/index.php';
    }

    public function create()
    {
        $categories = $this->categorieModel->getAllActives();
        $fournisseurs = $this->fournisseurModel->getAllActifs();
        
        // Set active menu item
        $path = 'Produits';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'],
                'prix' => $_POST['prix'],
                'categorie_id' => $_POST['categorie_id'],
                'fournisseur_id' => $_POST['fournisseur_id'],
                'actif' => true
            ];
            $this->produitModel->create($data);
            header('Location: /Produits');
            exit;
        }
        require __DIR__ . '/../Views/Produits/create.php';
    }

    public function edit($id)
    {
        $produit = $this->produitModel->findById($id);
        $categories = $this->categorieModel->getAllActives();
        $fournisseurs = $this->fournisseurModel->getAllActifs();
        
        // Set active menu item
        $path = 'Produits';
        
        if (!$produit) {
            $_SESSION['error'] = 'Produit non trouvé.';
            header('Location: /Produits');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'],
                'prix' => $_POST['prix'],
                'categorie_id' => $_POST['categorie_id'],
                'fournisseur_id' => $_POST['fournisseur_id'],
                'actif' => isset($_POST['actif']) ? (bool)$_POST['actif'] : true
            ];
            $this->produitModel->update($id, $data);
            header('Location: /Produits');
            exit;
        }
        require __DIR__ . '/../Views/Produits/edit.php';
    }

    public function delete($id)
    {
        $this->produitModel->delete($id);
        header('Location: /Produits');
        exit;
    }
} 