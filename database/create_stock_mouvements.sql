USE pharmacie_db;

CREATE TABLE IF NOT EXISTS stock_mouvements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    type_mouvement ENUM('entree', 'sortie') NOT NULL,
    date_mouvement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT,
    reference_type VARCHAR(50),
    reference_id INT,
    notes TEXT,
    FOREIGN KEY (produit_id) REFERENCES articles(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
); 