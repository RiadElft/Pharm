-- Nouvelle base de données pour gestion de pharmacie
CREATE DATABASE IF NOT EXISTS pharmacie_db;
USE pharmacie_db;

-- 1. Rôles utilisateurs
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE
);

-- 2. Utilisateurs
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- 3. Catégories d'articles
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 4. Fournisseurs
CREATE TABLE fournisseurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    contact VARCHAR(100),
    email VARCHAR(100),
    telephone VARCHAR(20),
    adresse TEXT,
    actif BOOLEAN DEFAULT TRUE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 5. Clients
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telephone VARCHAR(20),
    adresse TEXT,
    actif BOOLEAN DEFAULT TRUE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 6. Articles (Produits)
CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    categorie_id INT NOT NULL,
    fournisseur_id INT NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id),
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id)
);

-- 7. Stock
CREATE TABLE stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 0,
    notes TEXT DEFAULT NULL,
    date_dernier_mouvement TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    seuil_alerte INT DEFAULT 10,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id)
);

-- 7b. Mouvements de stock
CREATE TABLE stock_mouvements (
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

-- 8. Commandes d'achat (bons de commande)
CREATE TABLE commandes_achat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fournisseur_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_commande DATE NOT NULL,
    statut ENUM('brouillon','envoyee','recue','annulee') DEFAULT 'brouillon',
    notes TEXT,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- 9. Détails des commandes d'achat
CREATE TABLE details_commande_achat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    commande_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes_achat(id),
    FOREIGN KEY (article_id) REFERENCES articles(id)
);

-- 10. Ventes
CREATE TABLE ventes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT,
    utilisateur_id INT NOT NULL,
    date_vente DATE NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    statut ENUM('terminee','annulee') DEFAULT 'terminee',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- 11. Détails des ventes
CREATE TABLE details_vente (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vente_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (vente_id) REFERENCES ventes(id),
    FOREIGN KEY (article_id) REFERENCES articles(id)
);

-- 12. Paramètres généraux
CREATE TABLE parametres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cle VARCHAR(100) NOT NULL UNIQUE,
    valeur TEXT,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 13. Permissions
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- 14. Association rôles-permissions
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id)
);

-- 15. Association Produits-catégories (plusieurs-à-plusieurs)
CREATE TABLE Produits_categories (
    produit_id INT NOT NULL,
    categorie_id INT NOT NULL,
    PRIMARY KEY (produit_id, categorie_id),
    FOREIGN KEY (produit_id) REFERENCES articles(id),
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
);

-- 16. Association Produits-fournisseurs (plusieurs-à-plusieurs)
CREATE TABLE Produits_fournisseurs (
    produit_id INT NOT NULL,
    fournisseur_id INT NOT NULL,
    PRIMARY KEY (produit_id, fournisseur_id),
    FOREIGN KEY (produit_id) REFERENCES articles(id),
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id)
);

-- 17. Ajustements de stock
CREATE TABLE ajustements_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stock_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    type_ajustement ENUM('ajout', 'retrait') NOT NULL,
    quantite INT NOT NULL,
    raison TEXT NOT NULL,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stock_id) REFERENCES stock(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- 18. Alertes
CREATE TABLE alertes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('stock_bas', 'expiration_proche', 'expire') NOT NULL,
    produit_id INT NOT NULL,
    stock_id INT,
    message TEXT NOT NULL,
    statut ENUM('nouveau', 'vu', 'resolu') DEFAULT 'nouveau',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolu_le TIMESTAMP NULL,
    FOREIGN KEY (produit_id) REFERENCES articles(id),
    FOREIGN KEY (stock_id) REFERENCES stock(id)
);

-- 19. Configuration des alertes
CREATE TABLE configurations_alertes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('stock_bas', 'expiration', 'rupture') NOT NULL,
    seuil INT,
    delai_notification INT,
    notification_email BOOLEAN DEFAULT TRUE,
    actif BOOLEAN DEFAULT TRUE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 20. Logs d'audit
CREATE TABLE logs_audit (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    adresse_ip VARCHAR(45),
    user_agent TEXT,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Index pour la performance
CREATE INDEX idx_articles_nom ON articles(nom);
CREATE INDEX idx_stock_article ON stock(article_id);
CREATE INDEX idx_stock_fournisseur ON stock(article_id);
CREATE INDEX idx_ventes_client ON ventes(client_id);
CREATE INDEX idx_ventes_utilisateur ON ventes(utilisateur_id);
CREATE INDEX idx_details_vente_vente ON details_vente(vente_id);
CREATE INDEX idx_details_vente_article ON details_vente(article_id); 