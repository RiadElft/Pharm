-- Sample data for supplier orders (commandes_achat)
INSERT INTO commandes_achat (fournisseur_id, utilisateur_id, date_commande, statut, notes) VALUES
(1, 1, '2024-03-15', 'recue', 'Commande trimestrielle de médicaments génériques'),
(2, 1, '2024-03-16', 'envoyee', 'Commande urgente d''antibiotiques'),
(3, 1, '2024-03-17', 'brouillon', 'Réapprovisionnement stock printemps'),
(4, 1, '2024-03-18', 'recue', 'Commande mensuelle régulière');

-- Sample data for order details (details_commande_achat)
-- For Saidal (commande 1)
INSERT INTO details_commande_achat (commande_id, article_id, quantite, prix_unitaire) VALUES
(1, 1, 100, 250),  -- Paracétamol
(1, 2, 50, 450),   -- Amoxicilline
(1, 3, 75, 180);   -- Ibuprofène

-- For Biopharm (commande 2)
INSERT INTO details_commande_achat (commande_id, article_id, quantite, prix_unitaire) VALUES
(2, 4, 30, 850),   -- Oméprazole
(2, 5, 40, 320),   -- Metformine
(2, 6, 25, 750);   -- Amlodipine

-- For El-Kendi (commande 3)
INSERT INTO details_commande_achat (commande_id, article_id, quantite, prix_unitaire) VALUES
(3, 7, 60, 280),   -- Aspirine
(3, 8, 45, 420),   -- Ranitidine
(3, 9, 35, 650);   -- Ciprofloxacine

-- For Pharmalliance (commande 4)
INSERT INTO details_commande_achat (commande_id, article_id, quantite, prix_unitaire) VALUES
(4, 10, 80, 150),  -- Vitamine C
(4, 1, 120, 240),  -- Paracétamol (réapprovisionnement)
(4, 3, 90, 175);   -- Ibuprofène (réapprovisionnement) 