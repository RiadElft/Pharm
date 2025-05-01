-- Add ID column to clients table
ALTER TABLE clients
ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST;

-- Update foreign key in ventes table
ALTER TABLE ventes
ADD CONSTRAINT fk_ventes_client
FOREIGN KEY (client_id) REFERENCES clients(id)
ON DELETE SET NULL
ON UPDATE CASCADE; 