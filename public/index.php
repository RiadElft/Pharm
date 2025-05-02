<?php

declare(strict_types=1);

// Error handling
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Required files
require_once __DIR__ . '/../app/Models/ModelInterface.php';
require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Controllers/BaseController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/StockController.php';

// French Models
require_once __DIR__ . '/../app/Models/Produit.php';
require_once __DIR__ . '/../app/Models/Categorie.php';
require_once __DIR__ . '/../app/Models/Client.php';
require_once __DIR__ . '/../app/Models/Fournisseur.php';
require_once __DIR__ . '/../app/Models/Stock.php';
require_once __DIR__ . '/../app/Models/Vente.php';
require_once __DIR__ . '/../app/Models/Commande.php';

// French Controllers
require_once __DIR__ . '/../app/Controllers/ProduitController.php';
require_once __DIR__ . '/../app/Controllers/CategorieController.php';
require_once __DIR__ . '/../app/Controllers/ClientController.php';
require_once __DIR__ . '/../app/Controllers/FournisseurController.php';
require_once __DIR__ . '/../app/Controllers/StockController.php';
require_once __DIR__ . '/../app/Controllers/UtilisateurController.php';
require_once __DIR__ . '/../app/Controllers/VenteController.php';
require_once __DIR__ . '/../app/Controllers/CommandeController.php';

// Load environment variables
$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_NAME'] = 'pharmacie_db';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASS'] = '';
$_ENV['DB_CHARSET'] = 'utf8mb4';
$_ENV['APP_ENV'] = 'development';

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('log_errors', true);
error_log("Script started");

// Start session
session_start();

// Basic routing (temporary until proper router is implemented)
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Get the directory name of the script
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
// Remove the script directory from path if it exists
if ($scriptDir !== '/') {
    $path = str_replace($scriptDir, '', $path);
}

$path = trim($path, '/');

// Debug information
error_log("Full Server Info:");
error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
error_log("SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME']);
error_log("DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT']);
error_log("PHP_SELF: " . $_SERVER['PHP_SELF']);
error_log("Original Request: " . $request);
error_log("Processed Path: " . $path);

// Simple router
try {
    switch ($path) {
        case '':
        case 'dashboard':
            if (!isset($_SESSION['user_id'])) {
                $redirectUrl = $scriptDir . '/login';
                $redirectUrl = str_replace('//', '/', $redirectUrl);
                error_log("Redirecting to: " . $redirectUrl);
                header('Location: ' . $redirectUrl);
                exit;
            }
            $dashboardController = new \App\Controllers\DashboardController();
            $dashboardController->index();
            break;
            
        case 'login':
            if (isset($_SESSION['user_id'])) {
                $redirectUrl = $scriptDir . '/dashboard';
                $redirectUrl = str_replace('//', '/', $redirectUrl);
                error_log("Redirecting to: " . $redirectUrl);
                header('Location: ' . $redirectUrl);
                exit;
            }
            require __DIR__ . '/../app/Views/auth/login.php';
            break;
            
        case 'auth/login':
            $auth = new \App\Controllers\AuthController();
            $auth->login();
            break;
            
        case 'auth/logout':
            $auth = new \App\Controllers\AuthController();
            $auth->logout();
            break;
            
        case 'suppliers':
            $content_view = 'suppliers.php';
            require __DIR__ . '/../app/Views/layout.php';
            break;
        case 'suppliers/add':
            $mode = 'add';
            $content_view = 'supplier_form.php';
            require __DIR__ . '/../app/Views/layout.php';
            break;
        case (preg_match('/^suppliers\/edit\/(\d+)$/', $path, $matches) ? true : false):
            $mode = 'edit';
            $supplier = []; // TODO: Load supplier data by $matches[1]
            $content_view = 'supplier_form.php';
            require __DIR__ . '/../app/Views/layout.php';
            break;
        case 'inventory':
            $content_view = 'inventory.php';
            require __DIR__ . '/../app/Views/layout.php';
            break;
        case 'sales':
            $content_view = 'sales.php';
            require __DIR__ . '/../app/Views/layout.php';
            break;
        case 'customers':
            $content_view = 'customers.php';
            require __DIR__ . '/../app/Views/layout.php';
            break;
        case 'reports':
            $content_view = 'reports.php';
            require __DIR__ . '/../app/Views/layout.php';
            break;
        case 'utilisateurs':
            $controller = new \App\Controllers\UtilisateurController();
            $controller->index();
            break;
        case 'utilisateurs/create':
            $controller = new \App\Controllers\UtilisateurController();
            $controller->create();
            break;
        case 'utilisateurs/store':
            $controller = new \App\Controllers\UtilisateurController();
            $controller->store();
            break;
        case (preg_match('/^utilisateurs\/edit\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\UtilisateurController();
            $controller->edit((int)$matches[1]);
            break;
        case (preg_match('/^utilisateurs\/update\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\UtilisateurController();
            $controller->update((int)$matches[1]);
            break;
        case (preg_match('/^utilisateurs\/delete\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\UtilisateurController();
            $controller->delete((int)$matches[1]);
            break;
        case 'categories':
            $controller = new \App\Controllers\CategorieController();
            $controller->index();
            break;
        case 'categories/create':
            $controller = new \App\Controllers\CategorieController();
            $controller->create();
            break;
        case (preg_match('/^categories\/edit\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\CategorieController();
            $controller->edit((int)$matches[1]);
            break;
        case (preg_match('/^categories\/delete\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\CategorieController();
            $controller->delete((int)$matches[1]);
            break;
        case 'fournisseurs':
            $controller = new \App\Controllers\FournisseurController();
            $controller->index();
            break;
        case 'fournisseurs/create':
            $controller = new \App\Controllers\FournisseurController();
            $controller->create();
            break;
        case 'fournisseurs/store':
            $controller = new \App\Controllers\FournisseurController();
            $controller->create();  // The create method handles both GET and POST
            break;
        case (preg_match('/^fournisseurs\/edit\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\FournisseurController();
            $controller->edit((int)$matches[1]);
            break;
        case (preg_match('/^fournisseurs\/delete\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\FournisseurController();
            $controller->delete((int)$matches[1]);
            break;
        case 'clients':
            $controller = new \App\Controllers\ClientController();
            $controller->index();
            break;
        case 'clients/create':
            $controller = new \App\Controllers\ClientController();
            $controller->create();
            break;
        case (preg_match('/^clients\/edit\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\ClientController();
            $controller->edit((int)$matches[1]);
            break;
        case (preg_match('/^clients\/delete\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\ClientController();
            $controller->delete((int)$matches[1]);
            break;
        case 'ventes':
            $controller = new \App\Controllers\VenteController();
            $controller->index();
            break;
        case 'ventes/create':
            $controller = new \App\Controllers\VenteController();
            $controller->create();
            break;
        case (preg_match('/^ventes\/view\/(\d+)$/', $path, $matches) ? true : false):
            error_log("ROUTER: Matched ventes/view/{id}. ID: " . $matches[1]);
            $controller = new \App\Controllers\VenteController();
            $controller->view((int)$matches[1]);
            break;
        case (preg_match('/^ventes\/delete\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\VenteController();
            $controller->delete((int)$matches[1]);
            break;
        case 'commandes':
            $controller = new \App\Controllers\CommandeController();
            $controller->index();
            break;
        case 'commandes/create':
            $controller = new \App\Controllers\CommandeController();
            $controller->create();
            break;
        case (preg_match('/^commandes\/getProductsBySupplier\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\CommandeController();
            $controller->getProductsBySupplier((int)$matches[1]);
            break;
        case (preg_match('/^commandes\/updateStatus\/(\d+)\/([a-z]+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\CommandeController();
            $controller->updateStatus((int)$matches[1], $matches[2]);
            break;
        case (preg_match('/^commandes\/view\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\CommandeController();
            $controller->view((int)$matches[1]);
            break;
        case 'stock':
            error_log("Accessing stock route");
            try {
                $controller = new \App\Controllers\StockController();
                $controller->index();
            } catch (\Exception $e) {
                error_log("Stock route error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                http_response_code(500);
                require __DIR__ . '/../app/Views/errors/500.php';
            }
            break;
        case (preg_match('/^stock\/edit\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\StockController();
            $controller->edit((int)$matches[1]);
            break;
        case (preg_match('/^stock\/update\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\StockController();
            $controller->update((int)$matches[1]);
            break;
        case 'Produits':
            $controller = new \App\Controllers\ProduitController();
            $controller->index();
            break;
        case 'Produits/create':
            $controller = new \App\Controllers\ProduitController();
            $controller->create();
            break;
        case (preg_match('/^Produits\/edit\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\ProduitController();
            $controller->edit((int)$matches[1]);
            break;
        case (preg_match('/^Produits\/delete\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new \App\Controllers\ProduitController();
            $controller->delete((int)$matches[1]);
            break;
            
        case 'parametres':
            // For now, just display a simple view since we don't have a dedicated controller
            require __DIR__ . '/../app/Views/parametres.php';
            break;
            
        case 'debug/tables':
            try {
                $dbInstance = \App\Config\Database::getInstance(); // Use the static getInstance method
                $result = $dbInstance->query('SHOW TABLES');
                echo "<h1>Database Tables</h1><pre>";
                while ($row = $result->fetch()) {
                    echo $row[0] . "\n";
                }
                echo "</pre>";
                exit;
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
            break;
            
        default:
            error_log("404 Not Found. Path: '" . $path . "'");
            http_response_code(404);
            require __DIR__ . '/../app/Views/errors/404.php';
            break;
    }
} catch (Exception $e) {
    error_log("Error occurred: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    require __DIR__ . '/../app/Views/errors/500.php';
} 