<?php
// core/Router.php
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Response.php';

class Router {
    private static array $routes = [];

    public static function get(string $path, array|callable $handler, array $middlewares = []): void {
        self::addRoute('GET', $path, $handler, $middlewares);
    }

    public static function post(string $path, array|callable $handler, array $middlewares = []): void {
        self::addRoute('POST', $path, $handler, $middlewares);
    }

    public static function put(string $path, array|callable $handler, array $middlewares = []): void {
        self::addRoute('PUT', $path, $handler, $middlewares);
    }

    public static function delete(string $path, array|callable $handler, array $middlewares = []): void {
        self::addRoute('DELETE', $path, $handler, $middlewares);
    }

    private static function addRoute(string $method, string $path, array|callable $handler, array $middlewares): void {
        $path = '/' . trim($path, '/');
        // Convert route like /students/{id} to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = "#^{$pattern}$#";

        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public static function dispatch(): void {
        $request = new Request();
        $method = $request->getMethod();
        $path = $request->getPath();

        // Support HTTP verb override via _method parameter in POST
        if ($method === 'POST' && !empty($request->input('_method'))) {
            $method = strtoupper($request->input('_method'));
        }

        foreach (self::$routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                
                // Execute Middlewares
                foreach ($route['middlewares'] as $middleware) {
                    if (is_callable($middleware)) {
                        $middleware($request);
                    } elseif (is_string($middleware) && class_exists($middleware)) {
                        $instance = new $middleware();
                        $instance->handle($request);
                    }
                }

                // Extract named route parameters
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func($handler, $request, $params);
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$controllerClass, $methodName] = $handler;
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $methodName)) {
                            call_user_func([$controller, $methodName], $request, $params);
                            return;
                        }
                    }
                }
            }
        }

        // 404 Not Found
        if ($request->isAjax()) {
            Response::error("Đường dẫn không tồn tại ({$path})", 404);
        } else {
            http_response_code(404);
            View::render('errors/404', ['path' => $path], 'auth');
        }
    }
}
