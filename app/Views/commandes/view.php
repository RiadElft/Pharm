<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Commande - Gestion de Pharmacie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --pharmacy-green: #3A8D2F;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php require_once __DIR__ . '/../../Views/partials/sidebar.php'; ?>

    <!-- Main Content Wrapper -->
    <div id="main-content" class="transition-all duration-300 ease-in-out ml-64">
        <!-- Header -->
        <header class="flex items-center justify-between px-8 py-6 bg-white shadow-sm">
            <div class="flex items-center gap-3">
                <img src="//public/generated-image.png" alt="Pharmacy Logo" class="h-12 w-12 rounded-full bg-white border border-gray-200">
                <span class="text-2xl font-bold text-[color:var(--pharmacy-green)]">GreenLeaf Pharmacy</span>
            </div>
            <div class="flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=User&background=3A8D2F&color=fff" alt="User" class="h-10 w-10 rounded-full border-2 border-[color:var(--pharmacy-green)]">
            </div>
        </header>

        <!-- Main Content -->
        <div class="px-8 py-6">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Détails de la Commande #<?= htmlspecialchars($commande['id']) ?></h1>
                    <a href="/commandes" class="text-[color:var(--pharmacy-green)] hover:underline">Retour aux commandes</a>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <!-- Order Information -->
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations de la Commande</h2>
                            <dl class="space-y-2">
                                <div class="flex">
                                    <dt class="w-32 font-medium text-gray-500">Fournisseur:</dt>
                                    <dd class="text-gray-900"><?= htmlspecialchars($commande['fournisseur_nom'] ?? 'Non spécifié') ?></dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 font-medium text-gray-500">Date:</dt>
                                    <dd class="text-gray-900"><?= htmlspecialchars($commande['date_commande']) ?></dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 font-medium text-gray-500">Statut:</dt>
                                    <dd>
                                        <?php
                                        $statusClasses = [
                                            'en_attente' => 'bg-gray-100 text-gray-700',
                                            'commandee' => 'bg-blue-100 text-blue-700',
                                            'livree' => 'bg-green-100 text-green-700',
                                            'annulee' => 'bg-red-100 text-red-700'
                                        ];
                                        $statusClass = $statusClasses[$commande['statut']] ?? 'bg-gray-100 text-gray-700';
                                        ?>
                                        <span class="<?= $statusClass ?> px-2 py-1 rounded-full text-xs">
                                            <?php
                                            $statusLabels = [
                                                'en_attente' => 'En attente',
                                                'commandee' => 'Commandée',
                                                'livree' => 'Livrée',
                                                'annulee' => 'Annulée'
                                            ];
                                            echo htmlspecialchars($statusLabels[$commande['statut']] ?? ucfirst($commande['statut']));
                                            ?>
                                        </span>
                                    </dd>
                                </div>
                                <?php if (!empty($commande['notes'])): ?>
                                    <div class="flex">
                                        <dt class="w-32 font-medium text-gray-500">Notes:</dt>
                                        <dd class="text-gray-900"><?= nl2br(htmlspecialchars($commande['notes'])) ?></dd>
                                    </div>
                                <?php endif; ?>
                            </dl>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>
                            <div class="space-y-2">
                                <?php if ($commande['statut'] === 'en_attente'): ?>
                                    <a href="/commandes/updateStatus/<?= $commande['id'] ?>/commandee" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                        Confirmer la commande
                                    </a>
                                <?php elseif ($commande['statut'] === 'commandee'): ?>
                                    <a href="/commandes/updateStatus/<?= $commande['id'] ?>/livree" class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                        Confirmer la réception
                                    </a>
                                <?php endif; ?>
                                <?php if ($commande['statut'] !== 'livree'): ?>
                                    <a href="/commandes/updateStatus/<?= $commande['id'] ?>/annulee" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')"
                                       class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                        Annuler la commande
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Détails des Produits</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Prix unitaire</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php 
                                    $total = 0;
                                    foreach ($details as $detail): 
                                        $sousTotal = $detail['quantite'] * $detail['prix_unitaire'];
                                        $total += $sousTotal;
                                    ?>
                                        <tr>
                                            <td class="px-4 py-2"><?= htmlspecialchars($detail['produit_nom'] ?? 'Produit inconnu') ?></td>
                                            <td class="px-4 py-2 text-right"><?= htmlspecialchars($detail['quantite']) ?></td>
                                            <td class="px-4 py-2 text-right"><?= number_format($detail['prix_unitaire'], 0, ',', ' ') ?> DA</td>
                                            <td class="px-4 py-2 text-right"><?= number_format($sousTotal, 0, ',', ' ') ?> DA</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="bg-gray-50 font-semibold">
                                        <td colspan="3" class="px-4 py-2 text-right">Total</td>
                                        <td class="px-4 py-2 text-right"><?= number_format($total, 0, ',', ' ') ?> DA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
</body>
</html> 