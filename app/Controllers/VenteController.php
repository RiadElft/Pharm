<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Produit;
use PDOException;
use InvalidArgumentException;

class VenteController extends BaseController
{
    private Vente $venteModel;
    private Client $clientModel;
    private Produit $produitModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->venteModel = new Vente();
        $this->clientModel = new Client();
        $this->produitModel = new Produit();
    }

    public function index()
    {
        try {
            $ventes = $this->venteModel->getAll();
            $clients = $this->clientModel->getAllActifs();
            
            $this->render('ventes/index', [
                'ventes' => $ventes,
                'clients' => $clients,
                'title' => 'Gestion des Ventes'
            ]);
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la récupération des ventes: " . $e->getMessage();
            $this->redirect('/dashboard');
        }
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateVenteData($_POST);
                
                $data = [
                    'client_id' => (int)$_POST['client_id'],
                    'date_vente' => date('Y-m-d H:i:s'),
                    'montant_total' => 0,
                    'statut' => 'terminee'
                ];

                // Prepare details array for the vente
                $details = [];
                foreach ($_POST['produits'] as $produit) {
                    if (!empty($produit['id']) && !empty($produit['quantite']) && !empty($produit['prix'])) {
                        $details[] = [
                            'produit_id' => (int)$produit['id'],
                            'quantite' => (int)$produit['quantite'],
                            'prix_unitaire' => (float)$produit['prix']
                        ];
                    }
                }

                if (empty($details)) {
                    throw new InvalidArgumentException("Veuillez ajouter au moins un produit à la vente");
                }

                $data['details'] = $details;
                $venteId = $this->venteModel->create($data);
                
                $_SESSION['success'] = "Vente créée avec succès";
                $this->redirect('/ventes');
                return;
            }

            $clients = $this->clientModel->getAllActifs();
            $produits = $this->produitModel->getAllAvailable();
            
            $this->render('ventes/create', [
                'clients' => $clients,
                'produits' => $produits,
                'title' => 'Nouvelle Vente'
            ]);

        } catch (InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/ventes/create');
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la création de la vente: " . $e->getMessage();
            $this->redirect('/ventes/create');
        }
    }

    private function validateVenteData(array $data): void
    {
        if (!isset($data['client_id']) || !is_numeric($data['client_id'])) {
            throw new InvalidArgumentException("Veuillez sélectionner un client valide");
        }

        if (!isset($data['produits']) || !is_array($data['produits']) || empty($data['produits'])) {
            throw new InvalidArgumentException("Veuillez ajouter au moins un produit à la vente");
        }

        foreach ($data['produits'] as $produit) {
            if (!isset($produit['id'], $produit['quantite'], $produit['prix'])) {
                throw new InvalidArgumentException("Données de produit invalides");
            }

            $quantite = (int)$produit['quantite'];
            if ($quantite <= 0 || $quantite > 5) {
                throw new InvalidArgumentException("La quantité doit être entre 1 et 5 unités");
            }

            // Verify stock availability
            if (!$this->produitModel->checkStock((int)$produit['id'], $quantite)) {
                $produitInfo = $this->produitModel->findById((int)$produit['id']);
                throw new InvalidArgumentException("Stock insuffisant pour le produit " . $produitInfo['nom']);
            }
        }
    }

    public function view(int $id)
    {
        try {
            $vente = $this->venteModel->findById($id);
            if (!$vente) {
                throw new InvalidArgumentException("Vente non trouvée");
            }

            $details = $this->venteModel->getDetailsById($id);
            
            $this->render('ventes/view', [
                'vente' => $vente,
                'details' => $details,
                'title' => 'Détails de la Vente #' . $id
            ]);

        } catch (InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/ventes');
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la récupération des détails de la vente";
            $this->redirect('/ventes');
        }
    }

    public function delete(int $id)
    {
        try {
            $vente = $this->venteModel->findById($id);
            if (!$vente) {
                throw new InvalidArgumentException("Vente non trouvée");
            }

            // Get sale details before deletion to restore stock
            $details = $this->venteModel->getDetailsById($id);
            
            // Delete the sale
            $this->venteModel->delete($id);
            
            // Restore stock for each product
            foreach ($details as $detail) {
                $this->produitModel->updateStock(
                    (int)$detail['produit_id'],
                    (int)$detail['quantite']
                );
            }

            $_SESSION['success'] = "Vente supprimée avec succès";
            
        } catch (InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la suppression de la vente";
        }

        $this->redirect('/ventes');
    }
} 