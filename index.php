<?php
// ===============================================
// project_root/index.php  → Main API entry point
// Phase 1: Test DB + Router + Config
// ===============================================

// -----------------------------------------------
// Load config + core
// -----------------------------------------------
$config = require __DIR__ . '/config/db.php';   // returns config array (app, db, jwt, error)

require_once __DIR__ . '/core/database.php';
require_once __DIR__ . '/core/Router.php';

// -----------------------------------------------
// Global error settings (from config)
// -----------------------------------------------
if (isset($config['error'])) {
    $errorConfig = $config['error'];

    error_reporting($errorConfig['error_reporting'] ?? E_ALL);
    ini_set(
        'display_errors',
        !empty($errorConfig['display_errors']) ? '1' : '0'
    );
} else {
    // Fallback: dev-friendly
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// -----------------------------------------------
// Simple JSON helper (for Phase 1 test only)
// -----------------------------------------------
function send_json($data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

// -----------------------------------------------
// PDO getter using core/Database.php
// -----------------------------------------------
function get_pdo(): PDO
{
    // Our Database class exposes a static getInstance() that returns PDO
    $pdo = Database::getInstance();

    if (!($pdo instanceof PDO)) {
        throw new RuntimeException('Database::getInstance() did not return a PDO instance');
    }

    return $pdo;
}

// -----------------------------------------------
// Router setup
// -----------------------------------------------
$router = new Router();

// ---- Health check (no DB) ----
$router->get('/', function () {
    send_json([
        'success' => true,
        'message' => 'BloodSync API is running (Phase 1 OK)',
    ]);
});

// Optional explicit index route; useful if server hits /index.php directly
$router->get('/index.php', function () {
    send_json([
        'success' => true,
        'message' => 'BloodSync API is running (Phase 1 OK)',
    ]);
});

// API health alias
$router->get('/api/health', function () {
    send_json([
        'success' => true,
        'message' => 'BloodSync API is running (Phase 1 OK)',
    ]);
});

// ---- DB connection test ----
$router->get('/api/db-check', function () {
    try {
        $pdo  = get_pdo();
        $stmt = $pdo->query('SELECT 1');
        if ($stmt === false) {
            throw new RuntimeException('SELECT 1 failed');
        }

        send_json([
            'success' => true,
            'message' => 'Database connection OK',
        ]);
    } catch (Throwable $e) {
        // In dev this is fine; in prod don’t leak $e->getMessage()
        send_json([
            'success' => false,
            'message' => 'Database connection FAILED',
            'error'   => $e->getMessage(),
        ], 500);
    }
});

// -----------------------------------------------
// Dispatch
// You can also hit: index.php?route=/api/health while developing
// -----------------------------------------------
$routeParam = $_GET['route'] ?? null;

if ($routeParam !== null && $routeParam !== '') {
    $router->dispatch($routeParam, $_SERVER['REQUEST_METHOD'] ?? 'GET');
} else {
    $router->dispatch();
}