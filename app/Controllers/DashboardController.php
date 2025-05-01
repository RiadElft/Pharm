<?php

namespace App\Controllers;

use App\Models\Produit;
use App\Models\Vente;
use App\Models\User;
use App\Models\Stock;
use App\Models\Client;

class DashboardController {
    public function index() {
        $produitModel = new Produit();
        $userModel = new User();
        $venteModel = new Vente();
        $stockModel = new Stock();
        $clientModel = new Client();

        // Total sales (sum of all vente amounts)
        $totalSales = 0;
        $totalOrders = 0;
        
        try {
            // Get all ventes
            $ventes = $venteModel->all();
            foreach ($ventes as $vente) {
                $totalSales += (float)($vente['montant_total'] ?? 0);
                $totalOrders++;
            }
            
            // Total clients
            $totalClients = $clientModel->count(['actif' => true]) ?: 0;
            
            // Total stock (sum of all produit stock)
            $stockItems = $stockModel->getAllWithProduitDetails();
            $totalStock = 0;
            foreach ($stockItems as $item) {
                $totalStock += (int)($item['quantite'] ?? 0);
            }

            // Get monthly sales data for the chart
            $monthlySales = $venteModel->getMonthlySalesForCurrentYear();
            
            // Convert monthly sales data to arrays for the chart
            $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
            $salesData = array_values($monthlySales);

            // Get stock distribution by category
            $stockDistribution = $stockModel->getStockDistributionByCategory();
            $stockLabels = array_map(fn($item) => $item['nom'], $stockDistribution);
            $stockData = array_map(fn($item) => $item['quantite'], $stockDistribution);
            
        } catch (\Exception $e) {
            // Log error for debugging
            error_log("Dashboard error: " . $e->getMessage());
            
            // Set default values in case of error
            $totalSales = 0;
            $totalOrders = 0;
            $totalClients = 0;
            $totalStock = 0;
            $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
            $salesData = array_fill(0, 12, 0);
            $stockLabels = [];
            $stockData = [];
        }

        // Pass data to the view
        require __DIR__ . '/../Views/dashboard.php';
    }
} 