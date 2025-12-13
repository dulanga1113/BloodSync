<?php

// Base Model class – all models will extend this

require_once __DIR__ . '/database.php';

abstract class Model
{
    /**
     * Shared PDO connection instance
     *
     * @var \PDO
     */
    protected $db;

    public function __construct()
    {
        // Database::getInstance() returns a shared PDO connection
        $this->db = Database::getInstance();
    }

    /**
     * Low-level query helper.
     *
     * ✅ Convention for ALL child models:
     * - Always use NAMED placeholders in SQL (e.g. :email, :id)
     * - Always pass an ASSOCIATIVE array for $params:
     *
     *   $this->fetchOne(
     *       'SELECT * FROM users WHERE email = :email',
     *       [':email' => $email]
     *   );
     *
     * This keeps the whole project consistent.
     */
    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            $errorInfo = $this->db->errorInfo();
            throw new \RuntimeException(
                'Failed to prepare statement: ' . ($errorInfo[2] ?? 'Unknown error')
            );
        }

        if (!$stmt->execute($params)) {
            $errorInfo = $stmt->errorInfo();
            throw new \RuntimeException(
                'Failed to execute statement: ' . ($errorInfo[2] ?? 'Unknown error')
            );
        }

        return $stmt;
    }

    /**
     * Fetch a single row (or null if none).
     */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    /**
     * Fetch all rows as an array.
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Execute a write (INSERT/UPDATE/DELETE) and return affected row count.
     */
    protected function execute(string $sql, array $params = []): int
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Get last inserted ID (for SERIAL PKs etc.).
     */
    protected function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}