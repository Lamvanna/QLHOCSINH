<?php
// core/Request.php

class Request {
    private array $params;
    private array $query;
    private array $body;
    private array $files;
    private array $server;

    public function __construct() {
        $this->query = $_GET;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        
        // Handle JSON body or form post
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $input = file_get_contents('php://input');
            $this->body = json_decode($input, true) ?? [];
        } else {
            $this->body = $_POST;
        }

        $this->params = array_merge($this->query, $this->body);
    }

    public function getMethod(): string {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function getPath(): string {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        
        // Strip query string
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }

        // Remove base directory if present
        $base = BASE_URL;
        if (str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri = trim($uri, '/');
        return '/' . $uri;
    }

    public function isAjax(): bool {
        return (!empty($this->server['HTTP_X_REQUESTED_WITH']) && 
                strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               str_contains($this->server['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    public function input(string $key, mixed $default = null): mixed {
        return $this->params[$key] ?? $default;
    }

    public function all(): array {
        return $this->params;
    }

    public function file(string $key): ?array {
        return $this->files[$key] ?? null;
    }

    public function getIp(): string {
        return $this->server['HTTP_CLIENT_IP'] ?? 
               $this->server['HTTP_X_FORWARDED_FOR'] ?? 
               $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function getUserAgent(): string {
        return $this->server['HTTP_USER_AGENT'] ?? 'Unknown';
    }
}
