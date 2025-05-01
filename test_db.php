<?php

// Error handling
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set environment variables
$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_NAME'] = 'pharmacie_db';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASS'] = '';
$_ENV['DB_CHARSET'] = 'utf8mb4';

// Required files
require_once __DIR__ . '/app/Models/ModelInterface.php';
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Models/BaseModel.php';
require_once __DIR__ . '/app/Models/Client.php';

use App\Models\Client;

try {
    $clientModel = new Client();
    
    // Test total count
    $totalCount = $clientModel->count();
    echo "Total clients: " . $totalCount . "\n";
    
    // Test active count
    $activeCount = $clientModel->count(['actif' => true]);
    echo "Active clients: " . $activeCount . "\n";
    
    // Get all clients for debugging
    $allClients = $clientModel->getAll();
    echo "\nAll clients:\n";
    print_r($allClients);
    
    // Get active clients for debugging
    $activeClients = $clientModel->getAllActifs();
    echo "\nActive clients:\n";
    print_r($activeClients);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 