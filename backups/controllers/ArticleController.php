<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Fournisseur;

class ArticleController
{
    private Article $articleModel;
    private Categorie $categorieModel;
    private Fournisseur $fournisseurModel;

    public function __construct()
    {
        $this->articleModel = new Article();
        $this->categorieModel = new Categorie();
        $this->fournisseurModel = new Fournisseur();
    }

    public function index()
    {
        $articles = $this->articleModel->getAllActifs();
        $categories = $this->categorieModel->getAllActives();
        $fournisseurs = $this->fournisseurModel->getAllActifs();
        
        // Get category and supplier names
        foreach ($articles as &$article) {
            if (isset($article['categorie_id'])) {
                $categorie = $this->categorieModel->findById($article['categorie_id']);
                $article['categorie_nom'] = $categorie ? $categorie['nom'] : 'N/A';
            }
            
            if (isset($article['fournisseur_id'])) {
                $fournisseur = $this->fournisseurModel->findById($article['fournisseur_id']);
                $article['fournisseur_nom'] = $fournisseur ? $fournisseur['nom'] : 'N/A';
            }
        }
        unset($article); // Break the reference
        
        // Set active menu item
        $path = 'articles';
        
        require __DIR__ . '/../ViewsProduits/index.php';
    }

    public function create()
    {
        $categories = $this->categorieModel->getAllActives();
        $fournisseurs = $this->fournisseurModel->getAllActifs();
        
        // Set active menu item
        $path = 'articles';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'],
                'prix' => $_POST['prix'],
                'categorie_id' => $_POST['categorie_id'],
                'fournisseur_id' => $_POST['fournisseur_id'],
                'actif' => true
            ];
            $this->articleModel->create($data);
            header('Location: Produits');
            exit;
        }
        require __DIR__ . '/../ViewsProduits/create.php';
    }

    public function edit($id)
    {
        $article = $this->articleModel->findById($id);
        $categories = $this->categorieModel->getAllActives();
        $fournisseurs = $this->fournisseurModel->getAllActifs();
        
        // Set active menu item
        $path = 'articles';
        
        if (!$article) {
            $_SESSION['error'] = 'Article non trouvé.';
            header('Location: Produits');
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
            $this->articleModel->update($id, $data);
            header('Location: Produits');
            exit;
        }
        require __DIR__ . '/../ViewsProduits/edit.php';
    }

    public function delete($id)
    {
        $this->articleModel->delete($id);
        header('Location: Produits');
        exit;
    }
} 