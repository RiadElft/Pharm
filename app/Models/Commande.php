<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Commande extends BaseModel
{
    protected string $table = 'commandes_achat';

    public function getAllWithDetails(): array
    {
        $query = "SELECT ca.*, f.nom as fournisseur_nom, u.nom as utilisateur_nom,
                  COALESCE((SELECT SUM(dca.quantite * dca.prix_unitaire) 
                   FROM details_commande_achat dca 
                   WHERE dca.commande_id = ca.id), 0) as montant_total
                  FROM {$this->table} ca
                  JOIN fournisseurs f ON ca.fournisseur_id = f.id
                  JOIN utilisateurs u ON ca.utilisateur_id = u.id
                  ORDER BY ca.date_commande DESC";
        return $this->db->query($query)->fetchAll();
    }

    public function getDetailsById(int $commandeId): array
    {
        $query = "SELECT dca.*, p.nom as produit_nom
                  FROM details_commande_achat dca
                  JOIN produits p ON dca.produit_id = p.id
                  WHERE dca.commande_id = :commande_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['commande_id' => $commandeId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $query = "INSERT INTO {$this->table} (fournisseur_id, utilisateur_id, date_commande, statut, notes)
                  VALUES (:fournisseur_id, :utilisateur_id, :date_commande, :statut, :notes)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'fournisseur_id' => $data['fournisseur_id'],
            'utilisateur_id' => $data['utilisateur_id'],
            'date_commande' => $data['date_commande'],
            'statut' => $data['statut'] ?? 'brouillon',
            'notes' => $data['notes'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function addDetails(int $commandeId, array $details): void
    {
        $query = "INSERT INTO details_commande_achat (commande_id, produit_id, quantite, prix_unitaire)
                  VALUES (:commande_id, :produit_id, :quantite, :prix_unitaire)";
        $stmt = $this->db->prepare($query);
        
        foreach ($details as $detail) {
            try {
                $stmt->execute([
                    'commande_id' => $commandeId,
                    'produit_id' => $detail['produit_id'],
                    'quantite' => $detail['quantite'],
                    'prix_unitaire' => $detail['prix_unitaire']
                ]);
            } catch (\PDOException $e) {
                error_log("Error adding detail for commande $commandeId: " . $e->getMessage());
                error_log("Detail data: " . json_encode($detail));
                throw new \PDOException("Erreur lors de l'ajout des détails de la commande: " . $e->getMessage());
            }
        }
    }

    public function updateStatus(int $commandeId, string $status): bool
    {
        $query = "UPDATE {$this->table} SET statut = :statut WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $commandeId,
            'statut' => $status
        ]);
    }
} 