<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;

/**
 * Class Produit
 * 
 * Handles all product-related database operations including stock management
 */
class Produit extends BaseModel
{
    protected string $table = 'produits';

    /**
     * Get all available products with current stock levels
     * 
     * @return array List of available products with stock information
     * @throws PDOException If database query fails
     */
    public function getAllAvailable(): array
    {
        try {
            $query = "SELECT 
                        p.*,
                        s.quantite as stock_actuel
                      FROM {$this->table} p
                      LEFT JOIN stock s ON p.id = s.produit_id
                      WHERE p.actif = 1
                      AND (s.quantite > 0 OR s.quantite IS NULL)
                      ORDER BY p.nom ASC";

            $stmt = $this->db->prepare($query);
            if (!$stmt->execute()) {
                throw new PDOException("Failed to fetch available products");
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Produit::getAllAvailable: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la récupération des produits");
        }
    }

    /**
     * Find a product by its ID with current stock level
     * 
     * @param int $id Product ID
     * @return array|null Product data or null if not found
     * @throws PDOException If database query fails
     */
    public function findById(int $id): ?array
    {
        try {
            $query = "SELECT 
                        p.*,
                        s.quantite as stock_actuel
                      FROM {$this->table} p
                      LEFT JOIN stock s ON p.id = s.produit_id
                      WHERE p.id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Database error in Produit::findById: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la récupération du produit");
        }
    }

    /**
     * Update product stock quantity and record the adjustment
     * 
     * @param int $id Product ID
     * @param int $quantite Quantity to add (positive) or remove (negative)
     * @return bool True if successful
     * @throws PDOException If database operation fails
     */
    public function updateStock(int $id, int $quantite): bool
    {
        try {
            // Get or create stock record with row locking
            $stockQuery = "SELECT id, quantite FROM stock WHERE produit_id = :produit_id FOR UPDATE";
            $stmt = $this->db->prepare($stockQuery);
            $stmt->execute(['produit_id' => $id]);
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$stock) {
                // Create new stock record if it doesn't exist
                $createStockQuery = "INSERT INTO stock (produit_id, quantite) VALUES (:produit_id, 0)";
                $stmt = $this->db->prepare($createStockQuery);
                $stmt->execute(['produit_id' => $id]);
                $stockId = (int)$this->db->lastInsertId();
                $currentQuantite = 0;
            } else {
                $stockId = (int)$stock['id'];
                $currentQuantite = (int)$stock['quantite'];
            }

            // Validate new quantity won't be negative
            $newQuantite = $currentQuantite + $quantite;
            if ($newQuantite < 0) {
                throw new PDOException("La quantité en stock ne peut pas être négative pour le produit ID " . $id);
            }

            // Update stock quantity
            $updateStockQuery = "UPDATE stock SET quantite = :quantite WHERE id = :id";
            $stmt = $this->db->prepare($updateStockQuery);
            $stmt->execute([
                'quantite' => $newQuantite,
                'id' => $stockId
            ]);

            // Get or create system user for stock adjustments
            $userId = $this->getOrCreateSystemUser();

            // Record the adjustment with the valid user ID
            $ajustementQuery = "INSERT INTO ajustements_stock (
                stock_id,
                utilisateur_id,
                type_ajustement,
                quantite,
                raison
            ) VALUES (
                :stock_id,
                :utilisateur_id,
                :type_ajustement,
                :quantite,
                :raison
            )";

            $stmt = $this->db->prepare($ajustementQuery);
            $stmt->execute([
                'stock_id' => $stockId,
                'utilisateur_id' => $userId,
                'type_ajustement' => $quantite < 0 ? 'retrait' : 'ajout',
                'quantite' => abs($quantite),
                'raison' => 'Mouvement de stock via vente'
            ]);

            return true;

        } catch (PDOException $e) {
            error_log("Database error in Produit::updateStock: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            throw new PDOException("Une erreur est survenue lors de la mise à jour du stock: " . $e->getMessage());
        }
    }

    /**
     * Get or create system user for stock adjustments
     * 
     * @return int User ID
     * @throws PDOException If creation fails
     */
    private function getOrCreateSystemUser(): int
    {
        // First try to get existing system user
        $systemUserQuery = "SELECT u.id 
                           FROM utilisateurs u 
                           WHERE u.email = 'system@pharmacie.local' 
                           LIMIT 1";
        $stmt = $this->db->prepare($systemUserQuery);
        $stmt->execute();
        $systemUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($systemUser) {
            return (int)$systemUser['id'];
        }

        // If no system user exists, first ensure admin role exists
        $roleQuery = "SELECT id FROM roles WHERE nom = 'admin' LIMIT 1";
        $stmt = $this->db->prepare($roleQuery);
        $stmt->execute();
        $adminRole = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$adminRole) {
            // Create admin role
            $createRoleQuery = "INSERT INTO roles (nom) VALUES ('admin')";
            $stmt = $this->db->prepare($createRoleQuery);
            $stmt->execute();
            $roleId = (int)$this->db->lastInsertId();
        } else {
            $roleId = (int)$adminRole['id'];
        }

        // Create system user
        $createUserQuery = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, actif) 
                           VALUES ('System', 'system@pharmacie.local', :password, :role_id, 1)";
        $stmt = $this->db->prepare($createUserQuery);
        $stmt->execute([
            'password' => password_hash('SystemUser123!', PASSWORD_DEFAULT),
            'role_id' => $roleId
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Check if there is sufficient stock for a given quantity
     * 
     * @param int $id Product ID
     * @param int $quantite Quantity to check
     * @return bool True if sufficient stock available
     * @throws PDOException If database query fails
     */
    public function checkStock(int $id, int $quantite): bool
    {
        try {
            $query = "SELECT s.quantite 
                      FROM stock s 
                      WHERE s.produit_id = :id
                      FOR UPDATE"; // Add row locking for consistency
            
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // If no stock record exists, assume 0
            $currentStock = $result ? (int)$result['quantite'] : 0;
            return $currentStock >= abs($quantite);

        } catch (PDOException $e) {
            error_log("Database error in Produit::checkStock: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la vérification du stock");
        }
    }

    /**
     * Get all products in a specific category
     * 
     * @param int $categorieId Category ID
     * @return array List of products in the category
     */
    public function getByCategorie(int $categorieId): array
    {
        try {
            $query = "SELECT p.*, s.quantite as stock_actuel 
                      FROM {$this->table} p
                      LEFT JOIN stock s ON p.id = s.produit_id 
                      WHERE p.categorie_id = :categorie_id AND p.actif = TRUE";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['categorie_id' => $categorieId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Produit::getByCategorie: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la récupération des produits par catégorie");
        }
    }

    /**
     * Get all products from a specific supplier
     * 
     * @param int $fournisseurId Supplier ID
     * @return array List of products from the supplier
     * @throws PDOException If database query fails
     */
    public function getByFournisseur(int $fournisseurId): array
    {
        try {
            // First, check if the table exists
            try {
                $checkTable = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
                if ($checkTable->rowCount() === 0) {
                    throw new PDOException("Table {$this->table} does not exist");
                }
            } catch (PDOException $e) {
                error_log("Error checking table: " . $e->getMessage());
                throw $e;
            }

            // Start with a simple query first
            $query = "SELECT p.*, s.quantite as stock_actuel
                      FROM {$this->table} p
                      LEFT JOIN stock s ON p.id = s.produit_id 
                      WHERE p.fournisseur_id = :fournisseur_id AND p.actif = TRUE";
            
            error_log("Executing query: " . $query);
            error_log("With fournisseur_id: " . $fournisseurId);
            
            $stmt = $this->db->prepare($query);
            if (!$stmt->execute(['fournisseur_id' => $fournisseurId])) {
                $error = $stmt->errorInfo();
                error_log("Query execution error: " . json_encode($error));
                throw new PDOException("Failed to execute query: " . $error[2]);
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Found " . count($results) . " products");

            // Now get the last purchase price for each product
            foreach ($results as &$product) {
                try {
                    $priceQuery = "SELECT prix_unitaire 
                                  FROM details_commande_achat dca 
                                  JOIN commandes_achat ca ON dca.commande_id = ca.id 
                                  WHERE dca.produit_id = :produit_id 
                                  AND ca.fournisseur_id = :fournisseur_id 
                                  ORDER BY ca.date_commande DESC 
                                  LIMIT 1";
                    $priceStmt = $this->db->prepare($priceQuery);
                    $priceStmt->execute([
                        'produit_id' => $product['id'],
                        'fournisseur_id' => $fournisseurId
                    ]);
                    $price = $priceStmt->fetch(PDO::FETCH_COLUMN);
                    $product['prix_achat'] = $price ?: null;
                } catch (PDOException $e) {
                    error_log("Error getting price for product {$product['id']}: " . $e->getMessage());
                    $product['prix_achat'] = null;
                }
            }
            
            return $results;
            
        } catch (PDOException $e) {
            error_log("Database error in Produit::getByFournisseur: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new PDOException("Une erreur est survenue lors de la récupération des produits par fournisseur: " . $e->getMessage());
        }
    }

    /**
     * Get all active products with their current stock levels
     * 
     * @return array List of active products with stock information
     */
    public function getAllActifs(): array
    {
        try {
            $query = "SELECT p.*, s.quantite as stock_actuel 
                      FROM {$this->table} p
                      LEFT JOIN stock s ON p.id = s.produit_id
                      WHERE p.actif = TRUE
                      ORDER BY p.nom ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Produit::getAllActifs: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la récupération des produits actifs");
        }
    }

    /**
     * Get current stock level for a product
     * 
     * @param int $produitId Product ID
     * @return int|null Current stock quantity or null if no stock record exists
     */
    public function getStock(int $produitId): ?int
    {
        try {
            $query = "SELECT quantite FROM stock WHERE produit_id = :produit_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['produit_id' => $produitId]);
            $row = $stmt->fetch();
            return $row ? (int)$row['quantite'] : null;
        } catch (PDOException $e) {
            error_log("Database error in Produit::getStock: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la récupération du stock");
        }
    }
} 