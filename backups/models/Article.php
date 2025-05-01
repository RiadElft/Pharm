<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Article extends BaseModel
{
    protected string $table = 'articles';

    public function getByCategorie(int $categorieId): array
    {
        $query = "SELECT * FROM {$this->table} WHERE categorie_id = :categorie_id AND actif = TRUE";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['categorie_id' => $categorieId]);
        return $stmt->fetchAll();
    }

    public function getByFournisseur(int $fournisseurId): array
    {
        $query = "SELECT * FROM {$this->table} WHERE fournisseur_id = :fournisseur_id AND actif = TRUE";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['fournisseur_id' => $fournisseurId]);
        return $stmt->fetchAll();
    }

    public function getAllActifs(): array
    {
        $query = "SELECT * FROM {$this->table} WHERE actif = TRUE";
        return $this->db->query($query)->fetchAll();
    }

    public function getStock(int $articleId): ?int
    {
        $query = "SELECT quantite FROM stock WHERE article_id = :article_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['article_id' => $articleId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['quantite'] : null;
    }
} 