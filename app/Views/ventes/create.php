<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Vente - Gestion de Pharmacie</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <img src="/public/generated-image.png" alt="Pharmacy Logo" class="h-10 w-10 rounded-full bg-white border border-gray-200">
            <span class="text-xl font-bold text-[color:var(--pharmacy-green,#3A8D2F)]">GreenLeaf</span>
        </div>
        <nav class="flex-1 px-4 py-6">
            <ul class="space-y-2">
                <li><a href="/dashboard" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>Tableau de bord</a></li>
                <li>
                    <a href="/Produits" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>Produits</a>
                </li>
                <li><a href="/stock" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7h18M3 12h18M3 17h18"/></svg>Stock</a></li>
                <li><a href="/ventes" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-[color:var(--pharmacy-green)]/10 text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6"/></svg>Ventes</a></li>
            </ul>
        </nav>
    </div>

    <!-- Main Content Wrapper -->
    <div id="main-content" class="transition-all duration-300 ease-in-out ml-64">
        <!-- Header -->
        <header class="flex items-center justify-between px-8 py-6 bg-white shadow-sm">
            <div class="flex items-center gap-3">
                <img src="/public/generated-image.png" alt="Pharmacy Logo" class="h-12 w-12 rounded-full bg-white border border-gray-200">
                <span class="text-2xl font-bold text-[color:var(--pharmacy-green)]">GreenLeaf Pharmacy</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="/ventes" class="text-blue-600 hover:underline">Retour à la liste</a>
                <img src="https://ui-avatars.com/api/?name=User&background=3A8D2F&color=fff" alt="User" class="h-10 w-10 rounded-full border-2 border-[color:var(--pharmacy-green)]">
            </div>
        </header>

        <!-- Main Content -->
        <div class="px-8 py-6">
            <?php /** @var array $clients */ ?>
            <div class="max-w-xl mx-auto py-10">
                <h2 class="text-xl font-bold mb-6">Nouvelle vente</h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <form method="POST" action="/ventes/create" class="bg-white rounded-xl shadow p-6 space-y-4" id="venteForm">
                    <div>
                        <label class="block text-gray-700">Client</label>
                        <select name="client_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
                            <option value="">Sélectionner un client</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="produits-container">
                        <div class="produit-item space-y-4 border-b pb-4 mb-4">
                            <div>
                                <label class="block text-gray-700">Produit</label>
                                <select name="produits[0][id]" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 produit-select">
                                    <option value="">Sélectionner un produit</option>
                                    <?php foreach ($produits as $produit): ?>
                                        <option value="<?= $produit['id'] ?>" 
                                                data-prix="<?= $produit['prix'] ?>"
                                                data-stock="<?= $produit['stock_actuel'] ?>">
                                            <?= htmlspecialchars($produit['nom']) ?> 
                                            (Stock: <?= $produit['stock_actuel'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700">Quantité</label>
                                <input type="number" name="produits[0][quantite]" min="1" max="5" required 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 quantite-input" />
                                <span class="text-sm text-gray-500 stock-info"></span>
                                <p class="text-xs text-gray-500 mt-1">Maximum 5 unités par produit</p>
                            </div>
                            <div>
                                <label class="block text-gray-700">Prix unitaire (DA)</label>
                                <input type="number" name="produits[0][prix]" step="0.01" min="0" required 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 prix-input" readonly />
                            </div>
                            <div>
                                <label class="block text-gray-700">Sous-total (DA)</label>
                                <input type="text" class="mt-1 block w-full border-gray-300 rounded-md bg-gray-50 sous-total" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="button" id="ajouter-produit" 
                                class="text-[color:var(--pharmacy-green,#3A8D2F)] border border-current px-4 py-2 rounded-full font-semibold hover:bg-green-50 transition">
                            + Ajouter un produit
                        </button>
                        <div class="text-right">
                            <label class="block text-gray-700">Total (DA)</label>
                            <input type="text" id="total-vente" class="mt-1 block w-48 border-gray-300 rounded-md bg-gray-50" readonly />
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-[color:var(--pharmacy-green,#3A8D2F)] text-white px-4 py-2 rounded-full font-semibold hover:bg-green-700 transition">
                            Enregistrer la vente
                        </button>
                    </div>
                </form>
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

    // Vente form logic
    let produitCounter = 1;

    document.getElementById('ajouter-produit').addEventListener('click', function() {
        const container = document.getElementById('produits-container');
        const template = container.children[0].cloneNode(true);
        
        // Update names and clear values
        template.querySelectorAll('select, input').forEach(element => {
            if (element.name) {
                element.name = element.name.replace('[0]', `[${produitCounter}]`);
            }
            if (element.type !== 'button') {
                element.value = '';
            }
        });

        // Add delete button
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'text-red-600 hover:text-red-800 mt-2';
        deleteBtn.textContent = 'Supprimer';
        deleteBtn.onclick = function() {
            this.parentElement.remove();
            updateTotal();
        };
        template.appendChild(deleteBtn);

        container.appendChild(template);
        produitCounter++;
        
        // Reattach event listeners
        attachEventListeners(template);
    });

    function attachEventListeners(container) {
        container.querySelector('.produit-select').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const prix = option.dataset.prix;
            const stock = option.dataset.stock;
            const row = this.closest('.produit-item');
            
            row.querySelector('.prix-input').value = prix;
            row.querySelector('.stock-info').textContent = `Stock disponible: ${stock}`;
            // Set max to either 5 or the available stock, whichever is smaller
            const maxQuantity = Math.min(5, stock);
            row.querySelector('.quantite-input').max = maxQuantity;
            
            updateSousTotal(row);
        });

        const quantiteInput = container.querySelector('.quantite-input');
        quantiteInput.addEventListener('input', function() {
            // Ensure value stays between 1 and 5
            if (this.value > 5) {
                this.value = 5;
            } else if (this.value < 1) {
                this.value = 1;
            }
            updateSousTotal(this.closest('.produit-item'));
        });
    }

    function updateSousTotal(row) {
        const quantite = row.querySelector('.quantite-input').value;
        const prix = row.querySelector('.prix-input').value;
        const sousTotal = quantite * prix;
        row.querySelector('.sous-total').value = sousTotal.toFixed(2) + ' DA';
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.sous-total').forEach(input => {
            const value = parseFloat(input.value);
            if (!isNaN(value)) {
                total += value;
            }
        });
        document.getElementById('total-vente').value = total.toFixed(2) + ' DA';
    }

    // Form validation
    document.getElementById('venteForm').addEventListener('submit', function(e) {
        const produits = document.querySelectorAll('.produit-item');
        let hasProducts = false;

        produits.forEach(produit => {
            const select = produit.querySelector('.produit-select');
            const quantite = produit.querySelector('.quantite-input');
            if (select.value && quantite.value) {
                hasProducts = true;
            }
        });

        if (!hasProducts) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un produit à la vente');
        }
    });

    // Attach event listeners to initial row
    attachEventListeners(document.querySelector('.produit-item'));
    </script>
</body>
</html> 