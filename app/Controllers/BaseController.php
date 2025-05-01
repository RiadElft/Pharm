<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class BaseController
{
    protected function __construct()
    {
        $this->initializeSession();
        
        // Check authentication for all routes except login
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (!$this->isPublicRoute($currentPath)) {
            $this->checkAuthentication();
        }

        // Generate CSRF token if not exists
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function initializeSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();

            // Regenerate session ID periodically
            if (!isset($_SESSION['last_regeneration'])) {
                $_SESSION['last_regeneration'] = time();
            } elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }
    }

    private function isPublicRoute(string $path): bool
    {
        $publicRoutes = [
            '/login',
            '/auth/login',
            '/register',
            '/auth/register',
            '/password/reset',
            '/password/forgot'
        ];

        // Remove script directory from path if it exists
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptDir !== '/') {
            $path = str_replace($scriptDir, '', $path);
        }

        return in_array($path, $publicRoutes);
    }

    private function checkAuthentication(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page";
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            $this->redirect('/login');
            exit;
        }

        // Check session timeout (2 hours)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
            session_unset();
            session_destroy();
            $_SESSION['error'] = "Votre session a expiré. Veuillez vous reconnecter.";
            $this->redirect('/login');
            exit;
        }
        $_SESSION['last_activity'] = time();
    }

    protected function validateCSRF(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                die('Invalid CSRF token');
            }
        }
    }

    protected function render(string $view, array $data = []): void
    {
        // Add CSRF token to all views
        $data['csrf_token'] = $_SESSION['csrf_token'];

        // Extract data to make variables available in view
        extract($data);

        // Define the full path to the view file
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        // Check if view exists
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$view}");
        }

        // Start output buffering
        ob_start();

        // Include the view file
        require $viewFile;

        // Get the contents and clean the buffer
        $content = ob_get_clean();

        // Include the layout if it exists
        if (file_exists(__DIR__ . '/../Views/layouts/main.php')) {
            require __DIR__ . '/../Views/layouts/main.php';
        } else {
            echo $content;
        }
    }

    protected function redirect(string $path): void
    {
        if (headers_sent()) {
            echo '<script>window.location.href="' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '";</script>';
            echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '"></noscript>';
            exit;
        }

        header("Location: {$path}");
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function getPostData(): array
    {
        return $_POST;
    }

    protected function getQueryParams(): array
    {
        return $_GET;
    }

    protected function setFlashMessage(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function hasFlashMessage(string $type): bool
    {
        return isset($_SESSION['flash'][$type]);
    }

    protected function getFlashMessage(string $type): ?string
    {
        if (!$this->hasFlashMessage($type)) {
            return null;
        }

        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);

        return $message;
    }

    protected function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
} 