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

-- 3. Catégories de produits
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    code VARCHAR(20) UNIQUE,
    parent_id INT DEFAULT NULL,
    niveau INT DEFAULT 1,
    ordre INT DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
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

-- 5. Produits
CREATE TABLE produits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    categorie_id INT NOT NULL,
    fournisseur_id INT NOT NULL,
    seuil_alerte INT DEFAULT 10,
    actif BOOLEAN DEFAULT TRUE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id),
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id)
);

-- 6. Stock
CREATE TABLE stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 0,
    date_derniere_entree DATE,
    date_derniere_sortie DATE,
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- 7. Mouvements de stock
CREATE TABLE mouvements_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    type_mouvement ENUM('entree', 'sortie') NOT NULL,
    date_mouvement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT NOT NULL,
    notes TEXT,
    FOREIGN KEY (produit_id) REFERENCES produits(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- 8. Ventes
CREATE TABLE ventes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_vente VARCHAR(20) NOT NULL UNIQUE,
    utilisateur_id INT NOT NULL,
    date_vente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    montant_total DECIMAL(10,2) NOT NULL,
    montant_paye DECIMAL(10,2) NOT NULL,
    montant_restant DECIMAL(10,2) GENERATED ALWAYS AS (montant_total - montant_paye) STORED,
    statut ENUM('en_cours', 'terminee', 'annulee') DEFAULT 'en_cours',
    notes TEXT,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- 9. Détails des ventes
CREATE TABLE details_vente (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vente_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (vente_id) REFERENCES ventes(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- 10. Commandes fournisseurs
CREATE TABLE commandes_fournisseur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fournisseur_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_cours', 'livree', 'annulee') DEFAULT 'en_cours',
    montant_total DECIMAL(10,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- 11. Détails des commandes fournisseurs
CREATE TABLE details_commande_fournisseur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes_fournisseur(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Index pour optimiser les performances
CREATE INDEX idx_produits_nom ON produits(nom);
CREATE INDEX idx_stock_produit ON stock(produit_id);
CREATE INDEX idx_mouvements_stock_date ON mouvements_stock(date_mouvement);
CREATE INDEX idx_ventes_date ON ventes(date_vente);
CREATE INDEX idx_commandes_date ON commandes_fournisseur(date_commande); 