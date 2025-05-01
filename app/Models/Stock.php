<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * Stock repository for managing stock data.
 */
class Stock extends BaseModel
{
    protected string $table = 'stock';

    /**
     * Get stock quantity for a specific produit
     */
    public function getQuantiteByProduitId(int $produitId): int
    {
        $query = "SELECT quantite FROM {$this->table} WHERE produit_id = :produit_id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['produit_id' => $produitId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['quantite'] : 0;
    }

    /**
     * Update stock quantity for a produit, or create if not exists
     */
   

    /**
     * Get all stock entries with product details.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllWithProduitDetails(): array
    {
        $sql = 'SELECT s.id, s.produit_id, s.quantite, p.nom AS produit_nom, p.prix
                FROM stock s
                JOIN Produits p ON s.produit_id = p.id
                ORDER BY p.nom ASC';
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Find a stock entry by product ID.
     *
     * @param int $produitId
     * @return array<string, mixed>|null
     */
    public function findByProduitId(int $produitId): ?array
    {
        $sql = 'SELECT * FROM stock WHERE produit_id = :produit_id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['produit_id' => $produitId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Update stock quantity for a product.
     *
     * @param int $produitId
     * @param int $quantite
     * @return bool
     */
    public function updateQuantite(int $produitId, int $quantite): bool
    {
        $sql = 'UPDATE stock SET quantite = :quantite WHERE produit_id = :produit_id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'quantite'    => $quantite,
            'produit_id'  => $produitId,
        ]);
    }

    /**
     * Create a new stock entry.
     *
     * @param array<string, mixed> $data
     * @return int Inserted ID
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO stock (produit_id, quantite) VALUES (:produit_id, :quantite)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'produit_id' => $data['produit_id'],
            'quantite'   => $data['quantite']
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Get stock distribution by product category
     * 
     * @return array Array of categories with their total stock quantities
     */
    public function getStockDistributionByCategory(): array
    {
        $sql = 'SELECT 
                    c.nom as categorie_nom,
                    COALESCE(SUM(s.quantite), 0) as total_stock
                FROM categories c
                LEFT JOIN Produits p ON p.categorie_id = c.id
                LEFT JOIN stock s ON s.produit_id = p.id
                GROUP BY c.id, c.nom
                ORDER BY total_stock DESC';
        
        try {
            $stmt = $this->db->query($sql);
            if (!$stmt) {
                return [];
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function($row) {
                return [
                    'nom' => $row['categorie_nom'],
                    'quantite' => (int)$row['total_stock']
                ];
            }, $results);
        } catch (\PDOException $e) {
            error_log("Error getting stock distribution: " . $e->getMessage());
            return [];
        }
    }
} 