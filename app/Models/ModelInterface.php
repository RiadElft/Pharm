<?php

declare(strict_types=1);

namespace App\Models;

interface ModelInterface
{
    /**
     * Get all records
     */
    public function getAll(): array;

    /**
     * Find record by ID
     */
    public function findById(int $id): ?array;

    /**
     * Create new record
     */
    public function create(array $data): int;

    /**
     * Update existing record
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete record
     */
    public function delete(int $id): bool;

    /**
     * Count records
     */
    public function count(array $conditions = []): int;

    /**
     * Search records
     */
    public function search(string $term, array $fields = []): array;
} 