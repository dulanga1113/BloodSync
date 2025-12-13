<?php

// Base Controller for all API endpoints

abstract class Controller
{
    /**
     * Low-level JSON response helper.
     *
     * - Sets HTTP status code
     * - Sets Content-Type: application/json
     * - Encodes PHP data once (no double encoding)
     *
     * @param mixed $data
     * @param int   $statusCode
     */
    protected function json($data, int $statusCode = 200): void
    {
        // Set HTTP status
        http_response_code($statusCode);

        // Basic JSON API headers
        header('Content-Type: application/json; charset=utf-8');

        // You can centralize CORS here later if needed:
        // header('Access-Control-Allow-Origin: *');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        // Hard exit – controllers should not continue after sending response
        exit;
    }

    /**
     * Standard success response wrapper.
     *
     * Example:
     *   return $this->success(['token' => $jwt], 'Login successful');
     */
    protected function success($data = null, string $message = 'OK', int $statusCode = 200): void
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        $this->json($payload, $statusCode);
    }

    /**
     * Standard error response wrapper with unified format.
     *
     * Example:
     *   return $this->error('Invalid credentials', 401);
     *
     * Response shape:
     * {
     *   "success": false,
     *   "message": "Invalid credentials",
     *   "errors": { ... } // optional
     * }
     */
    protected function error(string $message, int $statusCode = 400, $errors = null): void
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        $this->json($payload, $statusCode);
    }

    /**
     * Read JSON body into an associative array.
     *
     * - Reads php://input
     * - Decodes JSON as array
     * - On invalid JSON, returns a 400 error and exits.
     */
    protected function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');

        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->error('Invalid JSON body', 400);
        }

        return $data;
    }

    /**
     * Shortcut for query string params: ?page=1&limit=10
     */
    protected function getQueryParams(): array
    {
        return $_GET ?? [];
    }

    /**
     * HTTP method helper (GET, POST, PUT, DELETE, etc.)
     */
    protected function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get a header value (case-insensitive).
     * Useful later for Authorization: Bearer <token>, X-Request-Id, etc.
     */
    protected function getHeader(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        // Fallback for environments where getallheaders() exists
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $headerName => $value) {
                if (strcasecmp($headerName, $name) === 0) {
                    return $value;
                }
            }
        }

        return null;
    }

    /**
     * Extract Bearer token from the Authorization header.
     *
     * Expected header format:
     *   Authorization: Bearer <jwt_here>
     *
     * @return string|null The token string, or null if missing/invalid format.
     */
    protected function getBearerToken(): ?string
    {
        $header = $this->getHeader('Authorization');

        if (!$header) {
            return null;
        }

        // Match "Bearer <token>" (case-insensitive for "Bearer")
        if (preg_match('/^\s*Bearer\s+(.+)\s*$/i', $header, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}