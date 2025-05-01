<?php

require_once __DIR__ . '/../test_db.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=pharmacie_db', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if we have any clients
    echo "Checking clients data:\n";
    $stmt = $db->query('SELECT * FROM clients LIMIT 5');
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    // Check if we have any users
    echo "\nChecking utilisateurs data:\n";
    $stmt = $db->query('SELECT * FROM utilisateurs LIMIT 5');
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    // Check ventes with their relationships
    echo "\nChecking ventes with relationships:\n";
    $query = "SELECT 
              v.id, v.client_id, v.utilisateur_id, v.date_vente,
              c.id as real_client_id, c.nom as client_nom,
              u.id as real_user_id, u.nom as utilisateur_nom
              FROM ventes v
              LEFT JOIN clients c ON v.client_id = c.id
              LEFT JOIN utilisateurs u ON v.utilisateur_id = u.id
              LIMIT 5";
    $stmt = $db->query($query);
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    // Check for any orphaned relationships
    echo "\nChecking for orphaned relationships:\n";
    echo "Ventes without valid clients:\n";
    $query = "SELECT v.* FROM ventes v 
              LEFT JOIN clients c ON v.client_id = c.id 
              WHERE v.client_id IS NOT NULL AND c.id IS NULL";
    $stmt = $db->query($query);
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    echo "\nVentes without valid users:\n";
    $query = "SELECT v.* FROM ventes v 
              LEFT JOIN utilisateurs u ON v.utilisateur_id = u.id 
              WHERE v.utilisateur_id IS NOT NULL AND u.id IS NULL";
    $stmt = $db->query($query);
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
} 