<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Client;
use PDOException;

class ClientController extends BaseController
{
    private Client $clientModel;

    public function __construct()
    {
        parent::__construct();
        $this->clientModel = new Client();
    }

    public function index(): void
    {
        try {
            $clients = $this->clientModel->getAllActifs();
            $this->render('clients/index', [
                'clients' => $clients,
                'title' => 'Gestion des Clients'
            ]);
        } catch (PDOException $e) {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la récupération des clients");
            $this->redirect('/dashboard');
        }
    }

    public function create(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRF();
                
                // Validate input data
                $data = $this->validateClientData($_POST);
                
                // Create client
                $clientId = $this->clientModel->create($data);
                
                $this->setFlashMessage('success', 'Client créé avec succès');
                $this->redirect('/clients');
                return;
            }

            $this->render('clients/create', [
                'title' => 'Nouveau Client'
            ]);
        } catch (PDOException $e) {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la création du client");
            $this->redirect('/clients/create');
        }
    }

    public function edit(int $id): void
    {
        try {
            $client = $this->clientModel->findById($id);
            if (!$client) {
                $this->setFlashMessage('error', 'Client non trouvé');
                $this->redirect('/clients');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRF();
                
                // Validate input data
                $data = $this->validateClientData($_POST);
                
                // Update client
                $this->clientModel->update($id, $data);
                
                $this->setFlashMessage('success', 'Client mis à jour avec succès');
                $this->redirect('/clients');
                return;
            }

            $this->render('clients/edit', [
                'client' => $client,
                'title' => 'Modifier le Client'
            ]);
        } catch (PDOException $e) {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la modification du client");
            $this->redirect('/clients');
        }
    }

    public function delete(int $id): void
    {
        try {
            $client = $this->clientModel->findById($id);
            if (!$client) {
                $this->setFlashMessage('error', 'Client non trouvé');
                $this->redirect('/clients');
                return;
            }

            // Check if client has any associated sales
            if ($this->hasClientSales($id)) {
                $this->setFlashMessage('error', 'Impossible de supprimer ce client car il a des ventes associées');
                $this->redirect('/clients');
                return;
            }

            $this->clientModel->delete($id);
            $this->setFlashMessage('success', 'Client supprimé avec succès');
            $this->redirect('/clients');
        } catch (PDOException $e) {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la suppression du client");
            $this->redirect('/clients');
        }
    }

    private function validateClientData(array $data): array
    {
        $errors = [];
        
        // Validate required fields
        if (empty($data['nom'])) {
            $errors[] = "Le nom est obligatoire";
        }

        // Validate email if provided
        if (!empty($data['email']) && !$this->validateEmail($data['email'])) {
            $errors[] = "L'adresse email n'est pas valide";
        }

        // Validate phone number if provided
        if (!empty($data['telephone']) && !preg_match('/^[0-9+\-\s()]{8,}$/', $data['telephone'])) {
            $errors[] = "Le numéro de téléphone n'est pas valide";
        }

        if (!empty($errors)) {
            $this->setFlashMessage('error', implode(', ', $errors));
            $this->redirect('/clients' . (isset($data['id']) ? '/edit/' . $data['id'] : '/create'));
        }

        return [
            'nom' => $this->sanitizeInput($data['nom']),
            'email' => !empty($data['email']) ? $this->sanitizeInput($data['email']) : null,
            'telephone' => !empty($data['telephone']) ? $this->sanitizeInput($data['telephone']) : null,
            'adresse' => !empty($data['adresse']) ? $this->sanitizeInput($data['adresse']) : null,
            'actif' => isset($data['actif']) ? (bool)$data['actif'] : true
        ];
    }

    private function hasClientSales(int $clientId): bool
    {
        try {
            $query = "SELECT COUNT(*) as count FROM ventes WHERE client_id = :client_id";
            $stmt = $this->clientModel->db->prepare($query);
            $stmt->execute(['client_id' => $clientId]);
            $result = $stmt->fetch();
            return (int)$result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error checking client sales: " . $e->getMessage());
            return true; // Assume has sales on error to prevent accidental deletion
        }
    }
} 