<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use PDOException;

class AuthController extends BaseController
{
    private User $userModel;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 300; // 5 minutes in seconds

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        
        // Set secure session parameters
        $this->setSecureSessionParams();
    }

    private function setSecureSessionParams(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    private function validatePassword(string $password): bool
    {
        $minLength = 8;
        return strlen($password) >= $minLength && 
               preg_match('/[A-Z]/', $password) && // At least one uppercase
               preg_match('/[a-z]/', $password) && // At least one lowercase
               preg_match('/[0-9]/', $password) && // At least one number
               preg_match('/[^A-Za-z0-9]/', $password); // At least one special char
    }

    private function checkLoginAttempts(string $email): bool
    {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
            $_SESSION['last_attempt'] = [];
        }

        $attempts = $_SESSION['login_attempts'][$email] ?? 0;
        $lastAttempt = $_SESSION['last_attempt'][$email] ?? 0;
        
        // If locked out and lockout period not expired
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS && (time() - $lastAttempt) < self::LOCKOUT_TIME) {
            $remainingTime = self::LOCKOUT_TIME - (time() - $lastAttempt);
            $_SESSION['error'] = sprintf(
                "Trop de tentatives de connexion. Veuillez réessayer dans %d minutes.", 
                ceil($remainingTime / 60)
            );
            return false;
        }

        // Reset attempts if lockout period expired
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS && (time() - $lastAttempt) >= self::LOCKOUT_TIME) {
            $_SESSION['login_attempts'][$email] = 0;
            $attempts = 0;
        }

        return true;
    }

    private function updateLoginAttempts(string $email): void
    {
        if (!isset($_SESSION['login_attempts'][$email])) {
            $_SESSION['login_attempts'][$email] = 0;
        }
        
        $_SESSION['login_attempts'][$email]++;
        $_SESSION['last_attempt'][$email] = time();
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = "Veuillez remplir tous les champs";
                $this->redirect('/login');
                return;
            }

            // Check for rate limiting
            if (!$this->checkLoginAttempts($email)) {
                $this->redirect('/login');
                return;
            }

            try {
                $user = $this->userModel->findByEmail($email);
                
                if (!$user) {
                    $this->updateLoginAttempts($email);
                    $_SESSION['error'] = "Email ou mot de passe incorrect";
                    $this->redirect('/login');
                    return;
                }

                if (!password_verify($password, $user['mot_de_passe'])) {
                    $this->updateLoginAttempts($email);
                    $_SESSION['error'] = "Email ou mot de passe incorrect";
                    $this->redirect('/login');
                    return;
                }

                // Reset login attempts on successful login
                unset($_SESSION['login_attempts'][$email]);
                unset($_SESSION['last_attempt'][$email]);

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_role'] = $user['role_id'];
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Redirect to intended URL if set, otherwise to dashboard
                $redirectTo = $_SESSION['intended_url'] ?? '/dashboard';
                unset($_SESSION['intended_url']); // Clear the intended URL
                
                $this->redirect($redirectTo);
                return;

            } catch (PDOException $e) {
                error_log("Login error: " . $e->getMessage());
                $_SESSION['error'] = "Une erreur est survenue lors de la connexion";
                $this->redirect('/login');
                return;
            }
        }

        // Display login form
        $this->render('auth/login', [
            'title' => 'Connexion'
        ]);
    }

    public function logout()
    {
        // Clear all session data
        $_SESSION = [];

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        $this->redirect('/login');
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'mot_de_passe' => $_POST['password'] ?? '',
                'role_id' => 2 // Default to regular user role
            ];

            if (empty($data['nom']) || empty($data['email']) || empty($data['mot_de_passe'])) {
                $_SESSION['error'] = "Veuillez remplir tous les champs";
                $this->redirect('/register');
                return;
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Format d'email invalide";
                $this->redirect('/register');
                return;
            }

            if (!$this->validatePassword($data['mot_de_passe'])) {
                $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial";
                $this->redirect('/register');
                return;
            }

            try {
                // Check if email already exists
                if ($this->userModel->findByEmail($data['email'])) {
                    $_SESSION['error'] = "Cet email est déjà utilisé";
                    $this->redirect('/register');
                    return;
                }

                $userId = $this->userModel->createUser($data);
                
                // Auto-login after registration
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $data['nom'];
                $_SESSION['user_role'] = $data['role_id'];
                
                // Regenerate session ID
                session_regenerate_id(true);
                
                $this->redirect('/dashboard');
                return;

            } catch (PDOException $e) {
                error_log("Registration error: " . $e->getMessage());
                $_SESSION['error'] = "Une erreur est survenue lors de l'inscription";
                $this->redirect('/register');
                return;
            }
        }

        $this->render('auth/register', [
            'title' => 'Inscription'
        ]);
    }
} 