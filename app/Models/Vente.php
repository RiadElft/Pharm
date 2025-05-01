<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use InvalidArgumentException;

class Vente extends BaseModel
{
    protected string $table = 'ventes';

    public function getAll(): array
    {
        try {
            $query = "SELECT 
                      v.id,
                      v.client_id,
                      v.date_vente,
                      v.statut,
                      v.montant_total,
                      COALESCE(c.nom, 'Client inconnu') as client_nom,
                      (SELECT GROUP_CONCAT(CONCAT(p.nom, ' (', dv.quantite, ')'))
                       FROM details_vente dv
                       JOIN produits p ON dv.produit_id = p.id
                       WHERE dv.vente_id = v.id) as produits
                      FROM {$this->table} v
                      LEFT JOIN clients c ON v.client_id = c.id
                      ORDER BY v.date_vente DESC";
            
            $stmt = $this->db->prepare($query);
            if (!$stmt->execute()) {
                throw new PDOException("Failed to fetch sales data");
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Database error in Vente::getAll: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la récupération des ventes");
        }
    }

    public function create(array $data): int
    {
        if (!isset($data['client_id']) || !is_numeric($data['client_id'])) {
            throw new InvalidArgumentException("Client ID invalide");
        }

        if (!isset($data['details']) || !is_array($data['details'])) {
            throw new InvalidArgumentException("Les détails de la vente sont requis");
        }

        try {
            $this->db->beginTransaction();

            // Create the sale
            $query = "INSERT INTO {$this->table} (
                client_id, 
                date_vente, 
                montant_total, 
                statut
            ) VALUES (
                :client_id, 
                :date_vente, 
                :montant_total, 
                :statut
            )";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                'client_id' => $data['client_id'],
                'date_vente' => $data['date_vente'] ?? date('Y-m-d H:i:s'),
                'montant_total' => 0.00, // Will be updated after adding details
                'statut' => $data['statut'] ?? 'terminee'
            ]);

            if (!$result) {
                throw new PDOException("Failed to create sale");
            }

            $venteId = (int)$this->db->lastInsertId();

            // Add sale details
            $detailsQuery = "INSERT INTO details_vente (
                vente_id, 
                produit_id, 
                quantite, 
                prix_unitaire
            ) VALUES (
                :vente_id, 
                :produit_id, 
                :quantite, 
                :prix_unitaire
            )";
            
            $detailsStmt = $this->db->prepare($detailsQuery);
            $produitModel = new Produit();
            
            foreach ($data['details'] as $detail) {
                if (!isset($detail['produit_id'], $detail['quantite'], $detail['prix_unitaire'])) {
                    throw new InvalidArgumentException("Détails de vente invalides");
                }

                if ($detail['quantite'] <= 0 || $detail['prix_unitaire'] < 0) {
                    throw new InvalidArgumentException("Quantité ou prix invalide");
                }

                // Check stock availability
                if (!$produitModel->checkStock($detail['produit_id'], $detail['quantite'])) {
                    throw new InvalidArgumentException("Stock insuffisant pour le produit ID " . $detail['produit_id']);
                }

                // Insert sale detail
                $detailsStmt->execute([
                    'vente_id' => $venteId,
                    'produit_id' => $detail['produit_id'],
                    'quantite' => $detail['quantite'],
                    'prix_unitaire' => $detail['prix_unitaire']
                ]);

                // Update stock
                $produitModel->updateStock($detail['produit_id'], -$detail['quantite']);
            }

            // Update total amount
            $this->updateTotal($venteId);

            $this->db->commit();
            return $venteId;

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error creating vente: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la création de la vente");
        }
    }

    public function addDetails(int $venteId, array $details): void
    {
        if (empty($details)) {
            throw new InvalidArgumentException("Les détails de la vente sont requis");
        }

        try {
            $query = "INSERT INTO details_vente (
                vente_id, 
                produit_id, 
                quantite, 
                prix_unitaire
            ) VALUES (
                :vente_id, 
                :produit_id, 
                :quantite, 
                :prix_unitaire
            )";
            
            $stmt = $this->db->prepare($query);
            $produitModel = new Produit($this->db);
            
            foreach ($details as $detail) {
                if (!isset($detail['produit_id'], $detail['quantite'], $detail['prix_unitaire'])) {
                    throw new InvalidArgumentException("Détails de vente invalides");
                }

                if ($detail['quantite'] <= 0 || $detail['prix_unitaire'] < 0) {
                    throw new InvalidArgumentException("Quantité ou prix invalide");
                }

                // Check stock availability
                if (!$produitModel->checkStock($detail['produit_id'], $detail['quantite'])) {
                    throw new InvalidArgumentException("Stock insuffisant pour le produit ID " . $detail['produit_id']);
                }

                // Insert sale detail
                $stmt->execute([
                    'vente_id' => $venteId,
                    'produit_id' => $detail['produit_id'],
                    'quantite' => $detail['quantite'],
                    'prix_unitaire' => $detail['prix_unitaire']
                ]);

                // Update stock
                $produitModel->updateStock($detail['produit_id'], -$detail['quantite']);
            }
        } catch (PDOException $e) {
            error_log("Error adding vente details: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de l'ajout des détails de la vente");
        }
    }

    public function getDetailsById(int $venteId): array
    {
        try {
            $query = "SELECT 
                        dv.*,
                        p.nom as produit_nom,
                        p.code as produit_code,
                        (dv.quantite * dv.prix_unitaire) as sous_total
                      FROM details_vente dv
                      LEFT JOIN produits p ON dv.produit_id = p.id
                      WHERE dv.vente_id = :vente_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute(['vente_id' => $venteId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching vente details: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la récupération des détails de la vente");
        }
    }

    public function updateTotal(int $venteId): bool
    {
        try {
            // First calculate the total
            $totalQuery = "SELECT COALESCE(SUM(quantite * prix_unitaire), 0) as total
                          FROM details_vente
                          WHERE vente_id = :vente_id";
            
            $stmt = $this->db->prepare($totalQuery);
            $stmt->execute(['vente_id' => $venteId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total = (float)$result['total'];

            // Then update the vente with the calculated total
            $updateQuery = "UPDATE {$this->table} 
                           SET montant_total = :montant_total
                           WHERE id = :vente_id";
            
            $stmt = $this->db->prepare($updateQuery);
            return $stmt->execute([
                'vente_id' => $venteId,
                'montant_total' => $total
            ]);
        } catch (PDOException $e) {
            error_log("Error updating vente total: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la mise à jour du montant total");
        }
    }

    public function getByClient(int $clientId): array
    {
        try {
            $query = "SELECT v.*, 
                      COALESCE(c.nom, 'Client inconnu') as client_nom,
                      (SELECT GROUP_CONCAT(CONCAT(p.nom, ' (', dv.quantite, ')'))
                       FROM details_vente dv
                       JOIN produits p ON dv.produit_id = p.id
                       WHERE dv.vente_id = v.id) as produits
                      FROM {$this->table} v
                      LEFT JOIN clients c ON v.client_id = c.id
                      WHERE v.client_id = :client_id
                      ORDER BY v.date_vente DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['client_id' => $clientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching client sales: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la récupération des ventes du client");
        }
    }

    public function getMonthlySalesForCurrentYear(): array
    {
        try {
            $query = "SELECT 
                        MONTH(date_vente) as mois,
                        COALESCE(SUM(montant_total), 0) as total
                      FROM {$this->table}
                      WHERE YEAR(date_vente) = YEAR(CURRENT_DATE)
                      GROUP BY MONTH(date_vente)
                      ORDER BY MONTH(date_vente)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Initialize all months with 0
            $monthlyData = array_fill(1, 12, 0);
            
            // Fill in actual data
            foreach ($results as $row) {
                $monthlyData[(int)$row['mois']] = (float)$row['total'];
            }
            
            return $monthlyData;
        } catch (PDOException $e) {
            error_log("Error getting monthly sales: " . $e->getMessage());
            throw new PDOException("Une erreur est survenue lors de la récupération des ventes mensuelles");
        }
    }
} 