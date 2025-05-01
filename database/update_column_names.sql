USE pharmacie_db;

-- Rename the articles table to produits
RENAME TABLE articles TO produits;

-- Update foreign key references in stock table
ALTER TABLE stock 
    DROP FOREIGN KEY stock_ibfk_1,
    CHANGE COLUMN article_id produit_id INT NOT NULL,
    ADD CONSTRAINT stock_ibfk_1 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update foreign key references in details_vente table
ALTER TABLE details_vente 
    DROP FOREIGN KEY details_vente_ibfk_2,
    CHANGE COLUMN article_id produit_id INT NOT NULL,
    ADD CONSTRAINT details_vente_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update foreign key references in details_commande_achat table
ALTER TABLE details_commande_achat 
    DROP FOREIGN KEY details_commande_achat_ibfk_2,
    CHANGE COLUMN article_id produit_id INT NOT NULL,
    ADD CONSTRAINT details_commande_achat_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update indexes
DROP INDEX idx_stock_article ON stock;
DROP INDEX idx_stock_fournisseur ON stock;
DROP INDEX idx_details_vente_article ON details_vente;
DROP INDEX idx_articles_nom ON produits;

CREATE INDEX idx_stock_produit ON stock(produit_id);
CREATE INDEX idx_details_vente_produit ON details_vente(produit_id);
CREATE INDEX idx_produits_nom ON produits(nom); 