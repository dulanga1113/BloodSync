<?php

class Database
{
    /**
     * @var Database|null
     */
    private static $instance = null;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Private constructor: creates a single PDO instance
     */
    private function __construct()
    {
        // Load config
        $config = require __DIR__ . '/../config/db.php';

        // Support both:
        // - new style: ['db' => [ 'driver' => ..., 'host' => ... ]]
        // - old style: [ 'driver' => ..., 'host' => ... ]
        $db = isset($config['db']) ? $config['db'] : $config;

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s',
            $db['driver'],
            $db['host'],
            $db['port'],
            $db['database']
        );

        try {
            $this->pdo = new \PDO(
                $dsn,
                $db['username'],
                $db['password'],
                isset($db['options']) ? $db['options'] : []
            );
        } catch (\PDOException $e) {
            // Don’t expose details in production – let upper layers handle/log this
            throw new \RuntimeException('Database connection failed.', 0, $e);
        }
    }

    /**
     * Get the shared PDO instance
     *
     * Usage in models:
     *   $pdo = Database::getInstance();
     *
     * @return \PDO
     */
    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->pdo;
    }
}