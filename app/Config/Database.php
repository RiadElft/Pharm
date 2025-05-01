<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::loadConfig();
            try {
                error_log("Attempting database connection with config: " . json_encode(array_diff_key(self::$config, ['password' => ''])));
                
                $dsn = sprintf(
                    "mysql:host=%s;dbname=%s;charset=%s",
                    self::$config['host'],
                    self::$config['database'],
                    self::$config['charset']
                );

                error_log("Using DSN: " . $dsn);

                self::$instance = new PDO(
                    $dsn,
                    self::$config['username'],
                    self::$config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
                
                // Test the connection by running a simple query
                $stmt = self::$instance->query("SELECT COUNT(*) as count FROM clients WHERE actif = TRUE");
                $result = $stmt->fetch();
                error_log("Found " . $result['count'] . " active clients");
                
                error_log("Database connection successful");
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                throw new PDOException("Database connection failed. Please check your configuration: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    private static function loadConfig(): void
    {
        // Load configuration from environment variables or use defaults
        self::$config = [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'database' => $_ENV['DB_NAME'] ?? 'pharmacie_db',
            'username' => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASS'] ?? '',
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4'
        ];

        // Log the configuration (excluding password)
        error_log("Database configuration loaded: " . json_encode(array_diff_key(self::$config, ['password' => ''])));
    }
} 