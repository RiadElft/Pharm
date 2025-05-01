<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Categorie extends BaseModel
{
    protected string $table = 'categories';

    public function getAllActives(): array
    {
        $query = "SELECT * FROM {$this->table} WHERE actif = TRUE";
        return $this->db->query($query)->fetchAll();
    }
} 