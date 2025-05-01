<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\Produit;

final class CommandeController
{
    private Commande $commandeModel;
    private Fournisseur $fournisseurModel;
    private Produit $produitModel;

    public function __construct()
    {
        $this->commandeModel = new Commande();
        $this->fournisseurModel = new Fournisseur();
        $this->produitModel = new Produit();
    }

    /**
     * Display the list of commandes
     */
    public function index(): void
    {
        $commandes = $this->commandeModel->getAllWithDetails();
        require __DIR__ . '/../Views/commandes/index.php';
    }

    public function create(): void
    {
        try {
            // Ensure we have a valid user ID before proceeding
            $userId = $this->ensureValidUser();
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    // Validate required fields
                    if (empty($_POST['fournisseur_id']) || !isset($_POST['produits']) || !is_array($_POST['produits'])) {
                        throw new \InvalidArgumentException('Données de commande invalides.');
                    }

                    // Start transaction
                    $this->commandeModel->db->beginTransaction();

                    // Create order with the validated user ID
                    $commandeData = [
                        'fournisseur_id' => (int)$_POST['fournisseur_id'],
                        'utilisateur_id' => $userId,
                        'date_commande' => date('Y-m-d'),
                        'statut' => 'en_attente',
                        'notes' => $_POST['notes'] ?? null
                    ];

                    $commandeId = $this->commandeModel->create($commandeData);

                    // Handle order details
                    $details = [];
                    foreach ($_POST['produits'] as $index => $produitId) {
                        if (!empty($_POST['quantites'][$index]) && !empty($_POST['prix'][$index])) {
                            $details[] = [
                                'produit_id' => (int)$produitId,
                                'quantite' => (int)$_POST['quantites'][$index],
                                'prix_unitaire' => (float)$_POST['prix'][$index]
                            ];
                        }
                    }

                    if (empty($details)) {
                        throw new \InvalidArgumentException('Aucun détail de commande valide fourni.');
                    }

                    $this->commandeModel->addDetails($commandeId, $details);

                    // Commit transaction
                    $this->commandeModel->db->commit();

                    $_SESSION['success'] = 'Commande créée avec succès.';
                    header('Location: /commandes');
                    exit;

                } catch (\Exception $e) {
                    $this->commandeModel->db->rollBack();
                    throw $e;
                }
            }

            $fournisseurs = $this->fournisseurModel->getAllActifs();
            require __DIR__ . '/../Views/commandes/create.php';
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la création de la commande: ' . $e->getMessage();
            $fournisseurs = $this->fournisseurModel->getAllActifs();
            require __DIR__ . '/../Views/commandes/create.php';
        }
    }

    /**
     * Ensures there is a valid user ID available, either from session or by creating a system user
     */
    private function ensureValidUser(): int
    {
        try {
            // First check session
            if (isset($_SESSION['user_id'])) {
                // Verify the user exists
                $query = "SELECT id FROM utilisateurs WHERE id = :id LIMIT 1";
                $stmt = $this->commandeModel->db->prepare($query);
                $stmt->execute(['id' => $_SESSION['user_id']]);
                if ($user = $stmt->fetch()) {
                    return (int)$user['id'];
                }
            }

            // Try to get system user
            $query = "SELECT id FROM utilisateurs WHERE email = 'system@pharmacie.local' LIMIT 1";
            $stmt = $this->commandeModel->db->prepare($query);
            $stmt->execute();
            if ($user = $stmt->fetch()) {
                return (int)$user['id'];
            }

            // Create system user
            $this->commandeModel->db->beginTransaction();
            try {
                // Ensure admin role exists
                $roleQuery = "SELECT id FROM roles WHERE nom = 'admin' LIMIT 1";
                $stmt = $this->commandeModel->db->prepare($roleQuery);
                $stmt->execute();
                $role = $stmt->fetch();

                $roleId = $role ? (int)$role['id'] : null;

                if (!$roleId) {
                    // Create admin role
                    $stmt = $this->commandeModel->db->prepare("INSERT INTO roles (nom) VALUES ('admin')");
                    $stmt->execute();
                    $roleId = (int)$this->commandeModel->db->lastInsertId();
                }

                // Create system user
                $stmt = $this->commandeModel->db->prepare(
                    "INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, actif) 
                     VALUES ('System', 'system@pharmacie.local', :password, :role_id, 1)"
                );
                $stmt->execute([
                    'password' => password_hash('SystemUser123!', PASSWORD_DEFAULT),
                    'role_id' => $roleId
                ]);

                $userId = (int)$this->commandeModel->db->lastInsertId();
                $this->commandeModel->db->commit();
                return $userId;

            } catch (\Exception $e) {
                $this->commandeModel->db->rollBack();
                throw new \RuntimeException("Impossible de créer l'utilisateur système: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            error_log("Error in ensureValidUser: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new \RuntimeException("Erreur lors de la vérification/création de l'utilisateur");
        }
    }

    public function view(int $id): void
    {
        $commande = $this->commandeModel->findById($id);
        if (!$commande) {
            $_SESSION['error'] = 'Commande non trouvée.';
            header('Location: /commandes');
            exit;
        }

        $details = $this->commandeModel->getDetailsById($id);
        require __DIR__ . '/../Views/commandes/view.php';
    }

    public function updateStatus(int $id, string $status): void
    {
        if (!in_array($status, ['en_attente', 'commandee', 'livree', 'annulee'])) {
            $_SESSION['error'] = 'Statut invalide.';
            header('Location: /commandes');
            exit;
        }

        try {
            $this->commandeModel->updateStatus($id, $status);
            $_SESSION['success'] = 'Statut de la commande mis à jour avec succès.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du statut: ' . $e->getMessage();
        }
        
        header('Location: /commandes/view/' . $id);
        exit;
    }

    public function delete(int $id): void
    {
        $this->commandeModel->delete($id);
        header('Location: /commandes');
        exit;
    }

    public function getProductsBySupplier(int $fournisseurId): void
    {
        header('Content-Type: application/json');
        try {
            error_log("Getting products for supplier ID: " . $fournisseurId);
            $produits = $this->produitModel->getByFournisseur($fournisseurId);
            error_log("Found " . count($produits) . " products");
            echo json_encode([
                'success' => true,
                'data' => $produits
            ]);
        } catch (\Exception $e) {
            error_log("Error in getProductsBySupplier: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération des produits: ' . $e->getMessage()
            ]);
        }
        exit;
    }
} 