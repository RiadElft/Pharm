<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Fournisseur extends BaseModel
{
    protected string $table = 'fournisseurs';

    public function getAllActifs(): array
    {
        $query = "SELECT * FROM {$this->table} WHERE actif = TRUE";
        return $this->db->query($query)->fetchAll();
    }
} 