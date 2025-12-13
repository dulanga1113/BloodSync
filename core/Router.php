<?php

// Simple Router for BloodSync

class Router
{
    /**
     * @var array
     *
     * Structure:
     * [
     *   'GET' => [
     *      [
     *          'path'        => '/api/donors/{id}',
     *          'pattern'     => '#^/api/donors/(?P<id>[^/]+)$#',
     *          'paramNames'  => ['id'],
     *          'handler'     => 'DonorController@show',
     *          'middleware'  => ['TokenGuard'],
     *      ],
     *      ...
     *   ],
     *   'POST' => [ ... ]
     * ]
     */
    protected $routes = [];

    // ---------- Route registration ----------

    /**
     * Generic add method as per checklist:
     * add($method, $path, $controller, $action, $middlewares = [])
     */
    public function add(
        string $method,
        string $path,
        string $controller,
        string $action,
        array $middleware = []
    ): void {
        $handler = $controller . '@' . $action;
        $this->addRoute(strtoupper($method), $path, $handler, $middleware);
    }

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    /**
     * Internal route registration helper.
     *
     * Supports:
     *   /api/donors/{id}
     *   /api/hospitals/{hospitalId}/requests/{requestId}
     */
    protected function addRoute(string $method, string $path, $handler, array $middleware = []): void
    {
        $normalizedPath = '/' . trim($path, '/');

        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        // Convert {param} to named capturing groups
        $paramNames = [];
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function ($matches) use (&$paramNames) {
                $paramNames[] = $matches[1];
                // Match anything up to the next slash
                return '(?P<' . $matches[1] . '>[^/]+)';
            },
            $normalizedPath
        );

        // If no params, $pattern == $normalizedPath
        $regex = '#^' . $pattern . '$#';

        $this->routes[$method][] = [
            'path'       => $normalizedPath,
            'pattern'    => $regex,
            'paramNames' => $paramNames,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    // ---------- Dispatching ----------

    /**
     * Dispatch the current HTTP request.
     *
     * Typically called from public/index.php:
     *
     *   $router = new Router();
     *   $router->add('POST', '/api/auth/login', 'AuthController', 'login');
     *   $router->dispatch();
     */
    public function dispatch(?string $requestUri = null, ?string $requestMethod = null): void
    {
        try {
            if ($requestUri === null) {
                $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
            }

            if ($requestMethod === null) {
                $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            }

            $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

            // Remove base path (e.g. /BloodSync/public)
            $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
            if ($scriptName !== '/' && $scriptName !== '\\' && strpos($path, $scriptName) === 0) {
                $path = substr($path, strlen($scriptName));
            }

            if ($path === '' || $path === false) {
                $path = '/';
            }

            $normalizedPath = '/' . trim($path, '/');

            $routesForMethod = $this->routes[$requestMethod] ?? [];

            foreach ($routesForMethod as $route) {
                if (preg_match($route['pattern'], $normalizedPath, $matches)) {
                    // Extract named params in the order of paramNames
                    $params = [];
                    foreach ($route['paramNames'] as $name) {
                        if (isset($matches[$name])) {
                            $params[$name] = $matches[$name];
                        }
                    }

                    // Run middleware stack
                    $this->runMiddleware($route['middleware']);

                    // Execute handler with route params
                    $this->runHandler($route['handler'], $params);
                    return;
                }
            }

            // No route matched
            $this->sendNotFound();
        } catch (\Throwable $e) {
            $this->sendServerError($e);
        }
    }

    // ---------- Internal helpers ----------

    protected function sendNotFound(): void
    {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'message' => 'Route not found',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }

    /**
     * Central 500 handler for uncaught exceptions.
     * Don’t leak details in production.
     */
    protected function sendServerError(\Throwable $e): void
    {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'message' => 'Internal Server Error',
            // In dev you COULD include this, but keep it off by default:
            // 'error'   => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // TODO: log $e somewhere (file, DB, etc.)
        exit;
    }

    /**
     * Run middleware stack.
     *
     * Each middleware can be:
     *  - string: 'TokenGuard' → new TokenGuard()->handle()
     *  - callable: function () { ... }
     *
     * Convention for class-based middleware:
     *   class TokenGuard {
     *       public function handle() { ... }
     *   }
     */
    protected function runMiddleware(array $middlewareStack): void
    {
        foreach ($middlewareStack as $middleware) {
            $result = null;

            if (is_string($middleware)) {
                // Assume a class name like 'TokenGuard'
                $className = $middleware;

                if (!class_exists($className)) {
                    $file = __DIR__ . '/../middleware/' . $className . '.php';
                    if (file_exists($file)) {
                        require_once $file;
                    }
                }

                if (!class_exists($className)) {
                    throw new \Exception("Middleware class not found: {$className}");
                }

                $instance = new $className();

                if (!method_exists($instance, 'handle')) {
                    throw new \Exception("Middleware {$className} must have a handle() method");
                }

                $result = $instance->handle();
            } elseif (is_callable($middleware)) {
                $result = $middleware();
            }

            // If middleware explicitly returns false, stop the chain.
            if ($result === false) {
                exit;
            }
        }
    }

    /**
     * Resolve and run the route handler.
     *
     * Supported formats:
     *  - callable
     *  - string: "AuthController@login"
     *  - string: "AuthController::login"
     *
     * Route params are passed as method arguments in the order
     * they appear in the URL, e.g.:
     *   /donors/{id} → function show($id) { ... }
     */
    protected function runHandler($handler, array $params = []): void
    {
        $params = array_values($params); // ensure numeric index for call_user_func_array

        // Direct callable
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (!is_string($handler)) {
            throw new \Exception('Invalid route handler type');
        }

        // String format
        $separator = null;
        if (strpos($handler, '@') !== false) {
            $separator = '@';
        } elseif (strpos($handler, '::') !== false) {
            $separator = '::';
        }

        if ($separator === null) {
            throw new \Exception('Invalid route handler string: ' . $handler);
        }

        list($controllerName, $method) = explode($separator, $handler, 2);

        // Load controller file from /controllers
        $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        }

        if (!class_exists($controllerName)) {
            throw new \Exception("Controller class not found: {$controllerName}");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            throw new \Exception("Method {$method} not found on controller {$controllerName}");
        }

        // Pass route params into controller method
        call_user_func_array([$controller, $method], $params);
    }
}