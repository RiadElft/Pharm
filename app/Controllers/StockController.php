<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Stock;
use App\Models\Produit;

/**
 * Contrôleur pour la gestion du stock.
 */
final class StockController
{
    private Stock $stockModel;
    private Produit $produitModel;

    public function __construct()
    {
        $this->stockModel   = new Stock();
        $this->produitModel = new Produit();
    }

    /**
     * Affiche la liste du stock.
     */
    public function index(): void
    {
        try {
            $stockEntries = $this->stockModel->getAllWithProduitDetails();
            require __DIR__ . '/../Views/stock/index.php';
        } catch (\Throwable $e) {
            $_SESSION['error'] = "Erreur lors du chargement du stock: " . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Affiche le formulaire d'édition du stock pour un produit.
     *
     * @param int $produitId
     */
    public function edit(int $produitId): void
    {
        try {
            $produit = $this->produitModel->findById($produitId);
            if (!$produit) {
                $_SESSION['error'] = "Produit non trouvé.";
                header('Location: /stock');
                exit;
            }
            $stockEntry = $this->stockModel->findByProduitId($produitId);
            if (!$stockEntry) {
                $stockEntry = [
                    'id'         => null,
                    'produit_id' => $produitId,
                    'quantite'   => 0,
                    'notes'      => ''
                ];
            }
            require __DIR__ . '/../Views/stock/edit.php';
        } catch (\Throwable $e) {
            $_SESSION['error'] = "Erreur lors du chargement du formulaire.";
            header('Location: /stock');
            exit;
        }
    }

    /**
     * Traite la mise à jour du stock pour un produit.
     *
     * @param int $produitId
     */
    public function update(int $produitId): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /stock/edit/' . $produitId);
                exit;
            }
            $quantite = (int)($_POST['quantite'] ?? 0);
            $notes    = trim($_POST['notes'] ?? '');
            $stockEntry = $this->stockModel->findByProduitId($produitId);
            if ($stockEntry) {
                $this->stockModel->updateQuantite($produitId, $quantite, $notes);
            } else {
                $this->stockModel->create([
                    'produit_id' => $produitId,
                    'quantite'   => $quantite,
                    'notes'      => $notes
                ]);
            }
            $_SESSION['success'] = "Stock mis à jour avec succès.";
            header('Location: /stock');
            exit;
        } catch (\Throwable $e) {
            $_SESSION['error'] = "Erreur lors de la mise à jour du stock.";
            header('Location: /stock/edit/' . $produitId);
            exit;
        }
    }
} 