-- Roles utilisateurs
INSERT INTO roles (nom) VALUES
('Administrateur'),
('Pharmacien'),
('Vendeur'),
('Gestionnaire de stock');

-- Utilisateur par défaut (mot de passe: admin123)
INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id) VALUES
('Admin', 'admin@pharmacie.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Categories de produits
INSERT INTO categories (nom, description) VALUES
('Médicaments génériques', 'Médicaments génériques disponibles en pharmacie'),
('Produits cosmétiques', 'Produits de beauté et soins personnels'),
('Matériel médical', 'Équipement et fournitures médicales'),
('Compléments alimentaires', 'Vitamines et suppléments nutritionnels');

-- Fournisseurs
INSERT INTO fournisseurs (nom, contact, email, telephone, adresse) VALUES
('Saidal Group', 'Mohamed Benali', 'contact@saidal.dz', '021234567', 'Zone Industrielle, Dar El Beida, Alger'),
('Biopharm', 'Karim Hamdi', 'service@biopharm.dz', '021987654', 'Route de Wilaya, Blida'),
('El-Kendi Pharma', 'Ahmed Kadi', 'info@elkendi.dz', '021456789', 'Zone d''activité, Sidi Abdellah, Alger'),
('Pharmalliance', 'Leila Mansouri', 'commercial@pharmalliance.dz', '021654321', 'Rue des frères Boughadou, Oran');

-- Clients (Pharmacies/Cliniques)
INSERT INTO clients (nom, email, telephone, adresse) VALUES
('Pharmacie El Amel', 'elamel@gmail.com', '0550123456', 'Rue Didouche Mourad, Alger Centre'),
('Clinique El Chifa', 'contact@elchifa.dz', '0660789123', 'Boulevard Krim Belkacem, Alger'),
('Pharmacie El Hayat', 'elhayat.pharma@gmail.com', '0770456789', 'Rue des Frères Bouadou, Tizi Ouzou'),
('Centre Medical El Razi', 'elrazi@gmail.com', '0550987654', 'Cité 1er Novembre, Oran');

-- Produits
INSERT INTO Produits (nom, prix, categorie_id, fournisseur_id) VALUES
('Paracétamol 500mg', 350.00, 1, 1),
('Amoxicilline 1g', 1200.00, 1, 1),
('Crème Argan El Kendi', 2500.00, 2, 3),
('Tensiomètre Digital', 12000.00, 3, 4),
('Vitamine D3 El Kendi', 1800.00, 4, 3),
('Glucophage 1000mg', 890.00, 1, 2),
('Savon Traditionnel à l''Huile d''Olive', 650.00, 2, 4),
('Thermomètre Infrarouge', 8500.00, 3, 4),
('Zinc Plus', 1500.00, 4, 2),
('Doliprane 1000mg', 450.00, 1, 1);

-- Stock initial
INSERT INTO stock (produit_id, quantite) VALUES
(1, 500),
(2, 300),
(3, 150),
(4, 50),
(5, 200),
(6, 400),
(7, 100),
(8, 75),
(9, 250),
(10, 600);

-- Commandes d'achat
INSERT INTO commandes_achat (fournisseur_id, utilisateur_id, date_commande, statut, notes) VALUES
(1, 1, '2024-05-01', 'recue', 'Commande mensuelle régulière'),
(2, 1, '2024-05-02', 'envoyee', 'Commande urgente'),
(3, 1, '2024-05-03', 'brouillon', 'Préparation stock été');

-- Détails des commandes d'achat
INSERT INTO details_commande_achat (commande_id, produit_id, quantite, prix_unitaire) VALUES
(1, 1, 1000, 280.00),
(1, 2, 500, 950.00),
(2, 6, 800, 720.00),
(2, 9, 400, 1200.00),
(3, 3, 200, 2000.00),
(3, 5, 300, 1500.00);

-- Ventes
INSERT INTO ventes (client_id, utilisateur_id, date_vente, montant_total) VALUES
(1, 1, '2024-05-01', 45000.00),
(2, 1, '2024-05-01', 68000.00),
(3, 1, '2024-05-02', 25000.00),
(1, 1, '2024-05-02', 35000.00),
(4, 1, '2024-05-03', 52000.00),
(2, 1, '2024-05-03', 28500.00),
(3, 1, '2024-05-04', 42000.00),
(1, 1, '2024-05-04', 22500.00),
(4, 1, '2024-05-05', 33000.00),
(2, 1, '2024-05-05', 48500.00);

-- Détails des ventes
INSERT INTO details_vente (vente_id, produit_id, quantite, prix_unitaire) VALUES
(1, 1, 50, 350.00),
(1, 2, 20, 1200.00),
(2, 4, 5, 12000.00),
(3, 3, 7, 2500.00),
(4, 6, 25, 890.00),
(4, 5, 10, 1800.00),
(5, 8, 5, 8500.00),
(5, 7, 12, 650.00),
(6, 9, 15, 1500.00),
(6, 10, 10, 450.00),
(7, 1, 40, 350.00),
(7, 3, 5, 2500.00),
(8, 2, 15, 1200.00),
(9, 4, 2, 12000.00),
(10, 5, 12, 1800.00);

-- Ajustements de stock
INSERT INTO ajustements_stock (stock_id, utilisateur_id, type_ajustement, quantite, raison) VALUES
(1, 1, 'retrait', 5, 'Produits endommagés'),
(2, 1, 'ajout', 100, 'Réception commande supplémentaire'),
(3, 1, 'retrait', 3, 'Erreur inventaire');

-- Alertes
INSERT INTO alertes (type, produit_id, stock_id, message, statut) VALUES
('stock_bas', 3, 3, 'Stock faible pour Crème Argan El Kendi', 'nouveau'),
('stock_bas', 8, 8, 'Stock faible pour Thermomètre Infrarouge', 'nouveau');

-- Paramètres
INSERT INTO parametres (cle, valeur) VALUES
('seuil_alerte_stock', '50'),
('devise', 'DA'),
('nom_pharmacie', 'Pharmacie Centrale'),
('adresse_pharmacie', 'Rue Larbi Ben M''hidi, Alger'); 