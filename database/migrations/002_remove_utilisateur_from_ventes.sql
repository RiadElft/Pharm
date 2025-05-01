-- Remove utilisateur_id from ventes table
ALTER TABLE ventes
    DROP FOREIGN KEY ventes_ibfk_2, -- Drop the foreign key constraint for utilisateur_id
    DROP COLUMN utilisateur_id;

-- Drop the index if it exists
DROP INDEX IF EXISTS idx_ventes_utilisateur ON ventes; 