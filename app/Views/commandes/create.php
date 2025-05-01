<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Commande - Gestion de Pharmacie</title>
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
                <li><a href="/Produits" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>Produits</a></li>
                <li><a href="/stock" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7h18M3 12h18M3 17h18"/></svg>Stock</a></li>
                <li><a href="/commandes" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-[color:var(--pharmacy-green)]/10 text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>Commandes</a></li>
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
                <a href="/commandes" class="text-blue-600 hover:underline">Retour à la liste</a>
                <img src="https://ui-avatars.com/api/?name=User&background=3A8D2F&color=fff" alt="User" class="h-10 w-10 rounded-full border-2 border-[color:var(--pharmacy-green)]">
            </div>
        </header>

        <!-- Main Content -->
        <div class="px-8 py-6">
            <div class="max-w-4xl mx-auto py-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Nouvelle Commande</h2>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="/commandes/create" method="POST" class="bg-white rounded-xl shadow p-6">
                    <!-- Fournisseur Selection -->
                    <div class="mb-6">
                        <label for="fournisseur_id" class="block text-sm font-medium text-gray-700 mb-2">Fournisseur</label>
                        <select name="fournisseur_id" id="fournisseur_id" required class="w-full rounded-lg border-gray-300 focus:border-[color:var(--pharmacy-green)] focus:ring focus:ring-[color:var(--pharmacy-green)]/20">
                            <option value="">Sélectionner un fournisseur</option>
                            <?php foreach ($fournisseurs as $fournisseur): ?>
                                <option value="<?= $fournisseur['id'] ?>"><?= htmlspecialchars($fournisseur['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (optionnel)</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-[color:var(--pharmacy-green)] focus:ring focus:ring-[color:var(--pharmacy-green)]/20"></textarea>
                    </div>

                    <!-- Products Table -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Produits</h3>
                        <div id="products-container">
                            <div class="grid grid-cols-12 gap-4 mb-4">
                                <div class="col-span-5">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Produit</label>
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix unitaire (DA)</label>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                                </div>
                            </div>
                            <div class="product-row grid grid-cols-12 gap-4 mb-4">
                                <div class="col-span-5">
                                    <select name="produits[]" required class="produit-select w-full rounded-lg border-gray-300 focus:border-[color:var(--pharmacy-green)] focus:ring focus:ring-[color:var(--pharmacy-green)]/20" disabled>
                                        <option value="">Sélectionner un produit</option>
                                    </select>
                                </div>
                                <div class="col-span-3">
                                    <input type="number" name="quantites[]" required min="1" class="quantite-input w-full rounded-lg border-gray-300 focus:border-[color:var(--pharmacy-green)] focus:ring focus:ring-[color:var(--pharmacy-green)]/20">
                                </div>
                                <div class="col-span-3">
                                    <input type="number" name="prix[]" required min="0.01" step="0.01" class="prix-input w-full rounded-lg border-gray-300 focus:border-[color:var(--pharmacy-green)] focus:ring focus:ring-[color:var(--pharmacy-green)]/20">
                                </div>
                                <div class="col-span-1">
                                    <button type="button" class="remove-row text-red-600 hover:text-red-800" style="display: none;">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-product" class="mb-6 text-[color:var(--pharmacy-green)] hover:text-green-700 font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter un produit
                        </button>

                        <!-- Total Amount -->
                        <div class="mb-6 text-right">
                            <span class="text-lg font-semibold text-gray-700">Total: </span>
                            <span id="total-amount" class="text-lg font-bold text-[color:var(--pharmacy-green)]">0 DA</span>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-[color:var(--pharmacy-green)] text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                                Créer la commande
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fournisseurSelect = document.getElementById('fournisseur_id');
            const productsContainer = document.getElementById('products-container');
            const addProductButton = document.getElementById('add-product');
            const totalAmount = document.getElementById('total-amount');
            const firstRow = productsContainer.querySelector('.product-row');

            // Function to calculate total
            function calculateTotal() {
                let total = 0;
                const rows = productsContainer.querySelectorAll('.product-row');
                rows.forEach(row => {
                    const quantite = parseFloat(row.querySelector('.quantite-input').value) || 0;
                    const prix = parseFloat(row.querySelector('.prix-input').value) || 0;
                    total += quantite * prix;
                });
                totalAmount.textContent = total.toLocaleString('fr-FR') + ' DA';
            }

            // Function to update product options
            async function updateProductOptions(fournisseurId) {
                const productSelects = document.querySelectorAll('.produit-select');
                try {
                    console.log('Fetching products for supplier:', fournisseurId);
                    // Get the script directory from the current URL
                    const scriptDir = window.location.pathname.split('/commandes')[0];
                    const url = `${scriptDir}/commandes/getProductsBySupplier/${fournisseurId}`.replace('//', '/');
                    console.log('Request URL:', url);
                    
                    const response = await fetch(url);
                    console.log('Response status:', response.status);
                    const data = await response.json();
                    console.log('Response data:', data);
                    
                    if (data.success) {
                        productSelects.forEach(select => {
                            select.innerHTML = '<option value="">Sélectionner un produit</option>';
                            data.data.forEach(produit => {
                                const option = document.createElement('option');
                                option.value = produit.id;
                                option.textContent = `${produit.nom} (Stock: ${produit.stock_actuel || 0})`;
                                option.dataset.lastPrice = produit.prix_achat || '';
                                select.appendChild(option);
                            });
                            select.disabled = false;
                        });
                    } else {
                        console.error('Server returned error:', data.message);
                    }
                } catch (error) {
                    console.error('Error fetching products:', error);
                }
            }

            // Handle supplier selection
            fournisseurSelect.addEventListener('change', function() {
                const fournisseurId = this.value;
                if (fournisseurId) {
                    updateProductOptions(fournisseurId);
                } else {
                    const productSelects = document.querySelectorAll('.produit-select');
                    productSelects.forEach(select => {
                        select.innerHTML = '<option value="">Sélectionner un produit</option>';
                        select.disabled = true;
                    });
                }
            });

            // Function to create new product row
            function createProductRow() {
                const newRow = firstRow.cloneNode(true);
                const removeButton = newRow.querySelector('.remove-row');
                removeButton.style.display = 'block';
                
                // Clear inputs
                newRow.querySelector('.produit-select').value = '';
                newRow.querySelector('.quantite-input').value = '';
                newRow.querySelector('.prix-input').value = '';

                // Add event listeners
                newRow.querySelector('.quantite-input').addEventListener('input', calculateTotal);
                newRow.querySelector('.prix-input').addEventListener('input', calculateTotal);
                removeButton.addEventListener('click', function() {
                    newRow.remove();
                    calculateTotal();
                });

                return newRow;
            }

            // Add product row button
            addProductButton.addEventListener('click', function() {
                const newRow = createProductRow();
                productsContainer.appendChild(newRow);
            });

            // Initial event listeners for first row
            firstRow.querySelector('.quantite-input').addEventListener('input', calculateTotal);
            firstRow.querySelector('.prix-input').addEventListener('input', calculateTotal);

            // Handle product selection to suggest last price
            productsContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('produit-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const row = e.target.closest('.product-row');
                    const prixInput = row.querySelector('.prix-input');
                    
                    if (selectedOption.dataset.lastPrice) {
                        prixInput.value = selectedOption.dataset.lastPrice;
                        calculateTotal();
                    }
                }
            });
        });
    </script>
</body>
</html> 