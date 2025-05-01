<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Pharmacie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --pharmacy-green: #3A8D2F;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Barre latérale -->
    <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg z-30 flex flex-col transition-transform duration-300 ease-in-out">
        <div class="flex items-center gap-3 px-6 py-6 border-b border-gray-100">
            <img src="public/generated-image.png" alt="Logo Pharmacie" class="h-10 w-10 rounded-full bg-white border border-gray-200">
            <span class="text-xl font-bold text-[color:var(--pharmacy-green)]">GreenLeaf</span>
        </div>
        <nav class="flex-1 px-4 py-6">
            <ul class="space-y-2">
                <li><a href="/dashboard" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-[color:var(--pharmacy-green)]/10 hover:text-[color:var(--pharmacy-green)] font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>Tableau de bord</a></li>
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
                <li><a href="/auth/logout" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-red-100 hover:text-red-600 font-medium"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/></svg>Déconnexion</a></li>
            </ul>
        </nav>
        <button id="sidebarToggle" class="absolute -right-4 top-6 bg-[color:var(--pharmacy-green)] text-white rounded-full p-2 shadow-lg focus:outline-none transition-transform"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
    </div>
    <div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-20 z-20 hidden" onclick="toggleSidebar()"></div>

    <!-- Contenu principal -->
    <div id="main-content" class="transition-all duration-300 ease-in-out ml-64">
        <!-- En-tête -->
        <header class="flex items-center justify-between px-8 py-6 bg-white shadow-sm">
            <div class="flex items-center gap-3">
                <img src="public/generated-image.png" alt="Logo Pharmacie" class="h-12 w-12 rounded-full bg-white border border-gray-200">
                <span class="text-2xl font-bold text-[color:var(--pharmacy-green)]">GreenLeaf Pharmacie</span>
            </div>
            <div class="flex items-center gap-4">
                <button class="bg-[color:var(--pharmacy-green)] text-white px-4 py-2 rounded-full font-semibold hover:bg-green-700 transition">Ajouter un produit</button>
                <img src="https://ui-avatars.com/api/?name=Utilisateur&background=3A8D2F&color=fff" alt="Utilisateur" class="h-10 w-10 rounded-full border-2 border-[color:var(--pharmacy-green)]">
            </div>
        </header>
        <!-- Zone de contenu principal -->
        <main class="p-8">
            <?php require __DIR__ . '/' . $content_view; ?>
        </main>
    </div>
    <script>
    // Logique de bascule de la barre latérale
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