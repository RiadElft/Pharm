<?php
// Removed session_start() and session check; handled in public/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --pharmacy-green: #3A8D2F;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg z-30 flex flex-col transition-transform duration-300 ease-in-out">
        <div class="flex items-center gap-3 px-6 py-6 border-b border-gray-100">
            <img src="public/generated-image.png" alt="Pharmacy Logo" class="h-10 w-10 rounded-full bg-white border border-gray-200">
            <span class="text-xl font-bold text-[color:var(--pharmacy-green)]">GreenLeaf</span>
        </div>
        <nav class="flex-1 px-4 py-6">
            <ul class="space-y-2">
                <li><a href="/dashboard" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-[color:var(--pharmacy-green)]/10 text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>Tableau de bord</a></li>
                <li>
                    <a href="/Produits" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>Produits</a>
                    <ul class="ml-10 mt-1 space-y-1">
                        <li><a href="/categories" class="block py-1 text-gray-700 hover:text-[color:var(--pharmacy-green)] font-medium">Catégories</a></li>
                    </ul>
                </li>
                <li><a href="/stock" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7h18M3 12h18M3 17h18"/></svg>Stock</a></li>
                <li><a href="/ventes" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6"/></svg>Ventes</a></li>
                <li><a href="/commandes" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6"/></svg>Commandes</a></li>
                <li><a href="/fournisseurs" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>Fournisseurs</a></li>
                <li><a href="/clients" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a7.5 7.5 0 0113 0"/></svg>Clients</a></li>
                <li><a href="/utilisateurs" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Utilisateurs</a></li>
                <li><a href="/parametres" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06a1.65 1.65 0 001.82.33h.09a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51h.09a1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82v.09a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>Paramètres</a></li>
                <li><a href="/auth/logout" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-red-100 hover:text-red-600 font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/></svg>Se déconnecter</a></li>
            </ul>
        </nav>
        <button id="sidebarToggle" class="absolute -right-4 top-6 bg-[color:var(--pharmacy-green)] text-white rounded-full p-2 shadow-lg focus:outline-none transition-transform"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
    </div>
    <div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-20 z-20 hidden" onclick="toggleSidebar()"></div>

    <!-- Main Content Wrapper -->
    <div id="main-content" class="transition-all duration-300 ease-in-out ml-64">
        <!-- Header -->
        <header class="flex items-center justify-between px-8 py-6 bg-white shadow-sm">
            <div class="flex items-center gap-3">
                <img src="public/generated-image.png" alt="Pharmacy Logo" class="h-12 w-12 rounded-full bg-white border border-gray-200">
                <span class="text-2xl font-bold text-[color:var(--pharmacy-green)]">GreenLeaf Pharmacy</span>
            </div>
           
        </header>

        <!-- Statistics Cards -->
        <section class="grid grid-cols-1 md:grid-cols-4 gap-6 px-8 py-8">
            <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center">
                <div class="bg-[color:var(--pharmacy-green)]/10 p-3 rounded-full mb-2">
                    <svg class="w-7 h-7 text-[color:var(--pharmacy-green)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 0v10l6 3"/></svg>
                </div>
                <div class="text-sm text-gray-600">Chiffre d'affaires</div>
                <div class="text-3xl font-bold"><?= number_format($totalSales, 0, ',', ' ') ?> DA</div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center">
                <div class="bg-[color:var(--pharmacy-green)]/10 p-3 rounded-full mb-2">
                    <svg class="w-7 h-7 text-[color:var(--pharmacy-green)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="text-3xl font-bold"><?= number_format($totalClients) ?></div>
                <div class="text-gray-500">Clients</div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center">
                <div class="bg-[color:var(--pharmacy-green)]/10 p-3 rounded-full mb-2">
                    <svg class="w-7 h-7 text-[color:var(--pharmacy-green)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h18v4H3z"/><path d="M3 7v13h18V7"/><path d="M16 13a4 4 0 1 1-8 0"/></svg>
                </div>
                <div class="text-3xl font-bold"><?= number_format($totalStock) ?></div>
                <div class="text-gray-500">Stock</div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center">
                <div class="bg-[color:var(--pharmacy-green)]/10 p-3 rounded-full mb-2">
                    <svg class="w-7 h-7 text-[color:var(--pharmacy-green)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 17l4 4 4-4m-4-5v9"/></svg>
                </div>
                <div class="text-3xl font-bold"><?= number_format($totalOrders) ?></div>
                <div class="text-gray-500">Commandes</div>
            </div>
        </section>

        <!-- Trends & Graphs -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 px-8 pb-8">
            <div class="bg-white rounded-2xl shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Tendance des ventes</h2>
                </div>
                <canvas id="salesChart" height="120"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Répartition du stock</h2>
                </div>
                <canvas id="inventoryChart" height="120"></canvas>
            </div>
        </section>

        <!-- Recent Activity -->
        <section class="px-8 pb-12">
            <div class="bg-white rounded-2xl shadow p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Activité récente</h2>
                <ul class="divide-y divide-gray-100">
                    <li class="py-3 flex items-center justify-between">
                        <span class="text-gray-600">Commande #1234 passée par Jean Dupont</span>
                        <span class="text-xs bg-[color:var(--pharmacy-green)]/20 text-[color:var(--pharmacy-green)] px-2 py-1 rounded-full">Terminée</span>
                    </li>
                    <li class="py-3 flex items-center justify-between">
                        <span class="text-gray-600">Alerte stock faible : Paracétamol</span>
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">Stock faible</span>
                    </li>
                    <li class="py-3 flex items-center justify-between">
                        <span class="text-gray-600">Nouveau client inscrit : Jeanne Martin</span>
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">Nouveau</span>
                    </li>
                </ul>
            </div>
        </section>
    </div>

    <script>
    // Sidebar toggle logic
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebar-backdrop');
    let sidebarOpen = true;

    function toggleSidebar() {
        sidebarOpen = !sidebarOpen;
        if (sidebarOpen) {
            sidebar.style.transform = 'translateX(0)';
            mainContent.style.marginLeft = '16rem';
            sidebarBackdrop.classList.add('hidden');
        } else {
            sidebar.style.transform = 'translateX(-100%)';
            mainContent.style.marginLeft = '0';
            sidebarBackdrop.classList.remove('hidden');
        }
    }
    sidebarToggle.addEventListener('click', toggleSidebar);
    // Responsive: close sidebar on small screens by default
    if (window.innerWidth < 768) {
        sidebarOpen = false;
        toggleSidebar();
    }
    window.addEventListener('resize', function() {
        if (window.innerWidth < 768 && sidebarOpen) {
            sidebarOpen = false;
            toggleSidebar();
        } else if (window.innerWidth >= 768 && !sidebarOpen) {
            sidebarOpen = true;
            toggleSidebar();
        }
    });
    </script>
    <script>
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: 'Ventes',
                    data: <?= json_encode($salesData) ?>,
                    borderColor: '#3A8D2F',
                    backgroundColor: 'rgba(58, 141, 47, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#3A8D2F',
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#F3F4F6' },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-DZ', {
                                    style: 'currency',
                                    currency: 'DZD',
                                    maximumFractionDigits: 0
                                }).format(value);
                            }
                        }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('fr-DZ', {
                                    style: 'currency',
                                    currency: 'DZD',
                                    maximumFractionDigits: 0
                                }).format(context.parsed.y);
                            }
                        }
                    }
                }
            }
        });
        // Inventory Breakdown Chart
        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        new Chart(inventoryCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($stockLabels) ?>,
                datasets: [{
                    data: <?= json_encode($stockData) ?>,
                    backgroundColor: [
                        '#3A8D2F',
                        '#6FCF97',
                        '#B5E7A0',
                        '#E0F2E9',
                        '#9DC88D',
                        '#C1E1C1',
                        '#A3C1AD',
                        '#85B79D'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                return `${label}: ${value} unités`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 