-- Add notes column to stock table
ALTER TABLE stock ADD COLUMN notes TEXT DEFAULT NULL AFTER quantite;
 
-- Add date_dernier_mouvement column if not exists (since it's used in the model but not in schema)
ALTER TABLE stock ADD COLUMN date_dernier_mouvement TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER notes; 