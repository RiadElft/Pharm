-- Ensure admin role exists
INSERT IGNORE INTO roles (nom) VALUES ('admin');

-- Ensure system user exists with admin role
INSERT IGNORE INTO utilisateurs (nom, email, mot_de_passe, role_id)
SELECT 'System', 'system@pharmacie.local', '', r.id
FROM roles r
WHERE r.nom = 'admin'
AND NOT EXISTS (
    SELECT 1 FROM utilisateurs WHERE email = 'system@pharmacie.local'
); 