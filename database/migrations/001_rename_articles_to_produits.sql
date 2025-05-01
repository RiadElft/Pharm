-- Rename articles table to produits
RENAME TABLE articles TO produits;

-- Update foreign keys in stock table
ALTER TABLE stock 
    DROP FOREIGN KEY stock_ibfk_1,
    CHANGE COLUMN article_id produit_id INT NOT NULL,
    ADD CONSTRAINT stock_ibfk_1 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update foreign keys in stock_mouvements table
ALTER TABLE stock_mouvements 
    DROP FOREIGN KEY stock_mouvements_ibfk_1,
    ADD CONSTRAINT stock_mouvements_ibfk_1 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update foreign keys in details_commande_achat table
ALTER TABLE details_commande_achat 
    DROP FOREIGN KEY details_commande_achat_ibfk_2,
    CHANGE COLUMN article_id produit_id INT NOT NULL,
    ADD CONSTRAINT details_commande_achat_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update foreign keys in details_vente table
ALTER TABLE details_vente 
    DROP FOREIGN KEY details_vente_ibfk_2,
    CHANGE COLUMN article_id produit_id INT NOT NULL,
    ADD CONSTRAINT details_vente_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update foreign keys in Produits_categories table
ALTER TABLE Produits_categories 
    DROP FOREIGN KEY Produits_categories_ibfk_1,
    ADD CONSTRAINT Produits_categories_ibfk_1 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update foreign keys in Produits_fournisseurs table
ALTER TABLE Produits_fournisseurs 
    DROP FOREIGN KEY Produits_fournisseurs_ibfk_1,
    ADD CONSTRAINT Produits_fournisseurs_ibfk_1 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update foreign keys in alertes table
ALTER TABLE alertes 
    DROP FOREIGN KEY alertes_ibfk_1,
    ADD CONSTRAINT alertes_ibfk_1 FOREIGN KEY (produit_id) REFERENCES produits(id);

-- Update indexes
DROP INDEX idx_articles_nom ON produits;
CREATE INDEX idx_produits_nom ON produits(nom);

DROP INDEX idx_stock_article ON stock;
CREATE INDEX idx_stock_produit ON stock(produit_id);

DROP INDEX idx_stock_fournisseur ON stock;
CREATE INDEX idx_stock_fournisseur ON stock(produit_id);

DROP INDEX idx_details_vente_article ON details_vente;
CREATE INDEX idx_details_vente_produit ON details_vente(produit_id); 